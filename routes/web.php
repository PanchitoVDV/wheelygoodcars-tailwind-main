<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ProfileController;
use App\Models\Car;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Debug route to check user role
Route::get('/check-user', function () {
    if (!Auth::check()) {
        return "Not logged in. Please <a href='" . route('login') . "'>login</a> first.";
    }
    
    $user = Auth::user();
    $output = "User: " . $user->name . " (ID: " . $user->id . ")<br>";
    $output .= "Email: " . $user->email . "<br>";
    $output .= "Role: " . ($user->role ?? 'none') . "<br>";
    $output .= "Is Aanbieder: " . ($user->isAanbieder() ? 'Yes' : 'No') . "<br>";
    
    if (!$user->isAanbieder()) {
        $output .= "<br><a href='" . route('become-aanbieder') . "'>Become Aanbieder</a>";
    }
    
    return $output;
});

// Route to become an aanbieder
Route::get('/become-aanbieder', function () {
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please login first.');
    }
    
    $user = Auth::user();
    $user->role = 'aanbieder';
    $user->save();
    
    return redirect()->route('check-user')->with('success', 'You are now an aanbieder!');
})->name('become-aanbieder');

// Public routes for car listings
Route::get('/cars/browse', [CarController::class, 'showPublicListing'])->name('cars.public');

Route::middleware('auth')->group(function () {
    // Test route to check if we can create a car
    Route::get('/test-car-create', function () {
        try {
            $car = new Car();
            $car->user_id = Auth::id(); // Gebruik ingelogde gebruiker
            $car->license_plate = 'TEST-' . rand(100, 999);
            $car->brand = 'Test Brand';
            $car->model = 'Test Model';
            $car->price = 10000;
            $car->mileage = 50000;
            $car->production_year = 2020;
            $car->color = 'Red';
            $car->views = 0;
            $car->save();
            
            return 'Auto succesvol aangemaakt met ID: ' . $car->id . '<br>Je bent ingelogd als: ' . Auth::user()->name . ' (ID: ' . Auth::id() . ')';
        } catch (\Exception $e) {
            return 'Fout bij aanmaken auto: ' . $e->getMessage();
        }
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Car routes - alleen voor aanbieders
    Route::middleware(['auth', 'aanbieder'])->group(function () {
        Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
        Route::get('/cars/create', [CarController::class, 'create'])->name('cars.create');
        Route::post('/cars/verify', [CarController::class, 'verifyLicensePlate'])->name('cars.verify');
        Route::get('/cars/verify', function() {
            return redirect()->route('cars.create');
        });
        Route::post('/cars', [CarController::class, 'store'])->name('cars.store');
        Route::get('/cars/select-tags', [CarController::class, 'selectTags'])->name('cars.select-tags');
        Route::post('/cars/finalize', [CarController::class, 'finalize'])->name('cars.finalize');
        Route::delete('/cars/{car}', [CarController::class, 'destroy'])->name('cars.destroy');
        Route::get('/cars/{car}/stats', [CarController::class, 'viewStats'])->name('cars.view-stats');
        
        // Tags bewerken routes
        Route::get('/cars/{car}/tags', [CarController::class, 'editTags'])->name('cars.edit-tags');
        Route::post('/cars/{car}/tags', [CarController::class, 'updateTags'])->name('cars.update-tags');
        
        // Status en prijs update routes
        Route::post('/cars/{car}/mark-as-sold', [CarController::class, 'markAsSold'])->name('cars.mark-as-sold');
        Route::post('/cars/{car}/mark-as-available', [CarController::class, 'markAsAvailable'])->name('cars.mark-as-available');
        Route::post('/cars/{car}/update-price', [CarController::class, 'updatePrice'])->name('cars.update-price');
    });
});

// Public route for car detail page (must be after the more specific routes)
Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');

// Admin routes - alleen voor beheerders
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/tags', [AdminController::class, 'tagStats'])->name('tags');
    Route::get('/suspicious-sellers', [AdminController::class, 'suspiciousSellers'])->name('suspicious-sellers');
    Route::get('/realtime-dashboard', [AdminController::class, 'realtimeDashboard'])->name('realtime-dashboard');
    Route::get('/dashboard-data', [AdminController::class, 'getDashboardData'])->name('dashboard-data');
});

require __DIR__.'/auth.php';
