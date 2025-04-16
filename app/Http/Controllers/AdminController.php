<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display a dashboard with admin statistics.
     */
    public function dashboard(): View
    {
        $usersCount = User::count();
        $carsCount = Car::count();
        $tagsCount = Tag::count();
        $soldCarsCount = Car::whereNotNull('sold_at')->count();
        
        return view('admin.dashboard', [
            'usersCount' => $usersCount,
            'carsCount' => $carsCount,
            'tagsCount' => $tagsCount,
            'soldCarsCount' => $soldCarsCount
        ]);
    }
    
    /**
     * Display tag usage statistics.
     */
    public function tagStats(): View
    {
        // Get tags with counts for both sold and unsold cars
        $tagStats = DB::table('tags')
            ->select('tags.id', 'tags.name', 'tags.color', 'tags.is_featured')
            ->selectRaw('COUNT(DISTINCT car_tag.car_id) as total_usage')
            ->selectRaw('COUNT(DISTINCT CASE WHEN cars.sold_at IS NOT NULL THEN car_tag.car_id ELSE NULL END) as sold_count')
            ->selectRaw('COUNT(DISTINCT CASE WHEN cars.sold_at IS NULL THEN car_tag.car_id ELSE NULL END) as available_count')
            ->leftJoin('car_tag', 'tags.id', '=', 'car_tag.tag_id')
            ->leftJoin('cars', 'car_tag.car_id', '=', 'cars.id')
            ->groupBy('tags.id', 'tags.name', 'tags.color', 'tags.is_featured')
            ->orderByDesc('total_usage')
            ->get();
        
        // Get the total counts for percentages
        $totalCars = Car::count();
        $soldCars = Car::whereNotNull('sold_at')->count();
        $availableCars = $totalCars - $soldCars;
        
        return view('admin.tag-stats', [
            'tagStats' => $tagStats,
            'totalCars' => $totalCars,
            'soldCars' => $soldCars,
            'availableCars' => $availableCars
        ]);
    }
    
    /**
     * Display a realtime dashboard with charts.
     */
    public function realtimeDashboard(): View
    {
        return view('admin.realtime-dashboard');
    }
    
    /**
     * Get realtime dashboard data in JSON format for AJAX requests.
     */
    public function getDashboardData()
    {
        $today = now()->startOfDay();
        
        // Totaal aantal auto's
        $totalCars = Car::count();
        
        // Aantal verkochte auto's
        $soldCars = Car::whereNotNull('sold_at')->count();
        
        // Aantal vandaag aangeboden auto's
        $carsAddedToday = Car::whereDate('created_at', $today)->count();
        
        // Aantal aanbieders (users met role aanbieder)
        $sellersCount = User::where('role', 'aanbieder')->count();
        
        // Aantal views vandaag
        $viewsToday = DB::table('cars')
            ->whereDate('cars.updated_at', $today)
            ->sum('views');
        
        // Gemiddeld aantal auto's per aanbieder
        $averageCarsPerSeller = $sellersCount > 0 ? round($totalCars / $sellersCount, 1) : 0;
        
        // Aantal auto's per merk (top 5)
        $carsByBrand = Car::select('brand', DB::raw('count(*) as total'))
            ->groupBy('brand')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
        
        // Auto's per prijsklasse
        $carsByPriceRange = [
            '< €5.000' => Car::where('price', '<', 5000)->count(),
            '€5.000 - €10.000' => Car::whereBetween('price', [5000, 10000])->count(),
            '€10.000 - €20.000' => Car::whereBetween('price', [10000, 20000])->count(),
            '€20.000 - €50.000' => Car::whereBetween('price', [20000, 50000])->count(),
            '> €50.000' => Car::where('price', '>', 50000)->count()
        ];
        
        // Verkochte auto's per maand (afgelopen 6 maanden)
        $salesByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Car::whereMonth('sold_at', $month->month)
                ->whereYear('sold_at', $month->year)
                ->count();
            $salesByMonth[$month->format('M Y')] = $count;
        }
        
        return response()->json([
            'totalCars' => $totalCars,
            'soldCars' => $soldCars,
            'availableCars' => $totalCars - $soldCars,
            'carsAddedToday' => $carsAddedToday,
            'sellersCount' => $sellersCount,
            'viewsToday' => $viewsToday,
            'averageCarsPerSeller' => $averageCarsPerSeller,
            'carsByBrand' => $carsByBrand,
            'carsByPriceRange' => $carsByPriceRange,
            'salesByMonth' => $salesByMonth,
            'timestamp' => now()->format('H:i:s')
        ]);
    }
    
    /**
     * Display suspicious sellers for review.
     */
    public function suspiciousSellers(): View
    {
        $currentYear = now()->year;
        $oneYearAgo = now()->subYear();
        
        // Get all sellers (users with role 'aanbieder')
        $sellers = User::where('role', 'aanbieder')
            ->withCount(['cars', 'cars as cars_without_tags_count' => function($query) {
                $query->whereDoesntHave('tags');
            }])
            ->withCount(['cars as low_price_cars_count' => function($query) {
                $query->where('price', '<', 1000);
            }])
            ->withCount(['cars as high_price_same_day_sold_count' => function($query) {
                $query->where('price', '>', 10000)
                    ->whereRaw('DATE(sold_at) = DATE(created_at)');
            }])
            ->with(['cars' => function($query) {
                $query->withCount('tags')
                    ->orderBy('created_at', 'desc');
            }])
            ->get();
        
        // Filter suspicious sellers
        $suspiciousSellers = $sellers->filter(function($seller) use ($oneYearAgo) {
            // Check if has no recent cars
            $hasRecentCars = $seller->cars->where('created_at', '>=', $oneYearAgo)->count() > 0;
            
            // Check if seller has suspicious cars (old but low mileage)
            $hasSuspiciousCars = $seller->cars->contains(function($car) {
                $carAge = now()->year - ($car->production_year ?? now()->year);
                return $carAge > 10 && $car->mileage < 50000;
            });
            
            // Check if all cars have low price
            $allLowPriceCars = $seller->cars_count > 0 && $seller->cars_count === $seller->low_price_cars_count;
            
            // Check if seller has many cars sold on the same day they were added
            $manySameDaySales = $seller->high_price_same_day_sold_count >= 3;
            
            // Check if seller has cars without tags
            $hasNoTags = $seller->cars_without_tags_count > 0;
            
            // Check if has phone number
            $hasPhoneNumber = !empty($seller->phone);
            
            // Mark as suspicious if any condition is met
            return (!$hasRecentCars && $seller->cars_count > 0) || 
                   $hasSuspiciousCars || 
                   $allLowPriceCars || 
                   $manySameDaySales || 
                   $hasNoTags ||
                   !$hasPhoneNumber;
        });
        
        return view('admin.suspicious-sellers', [
            'suspiciousSellers' => $suspiciousSellers
        ]);
    }
}
