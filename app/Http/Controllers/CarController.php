<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CarController extends Controller
{
    /**
     * Display a listing of the cars.
     */
    public function index(): View
    {
        $cars = Car::where('user_id', Auth::id())
                   ->whereNull('sold_at')
                   ->latest()
                   ->get();
        
        return view('cars.index', [
            'cars' => $cars
        ]);
    }
    
    /**
     * Show the form for creating a new car.
     */
    public function create(): View
    {
        return view('cars.create');
    }
    
    /**
     * Validate license plate and show car details for verification.
     */
    public function verifyLicensePlate(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|string|max:10',
        ]);
        
        $licensePlate = str_replace(['-', ' '], '', strtoupper($request->license_plate));
        
        try {
            // Fetch vehicle data from RDW API
            $response = Http::get("https://opendata.rdw.nl/resource/m9d7-ebf2.json?kenteken={$licensePlate}");
            
            if ($response->successful() && count($response->json()) > 0) {
                $rdwData = $response->json()[0];
                
                // Get additional details like fuel type
                $fuelResponse = Http::get("https://opendata.rdw.nl/resource/8ys7-d773.json?kenteken={$licensePlate}");
                $fuelData = [];
                if ($fuelResponse->successful() && count($fuelResponse->json()) > 0) {
                    $fuelData = $fuelResponse->json()[0];
                }
                
                // Map RDW data to our model
                $carData = [
                    'license_plate' => $licensePlate,
                    'brand' => $rdwData['merk'] ?? '',
                    'model' => $rdwData['handelsbenaming'] ?? '',
                    'production_year' => isset($rdwData['datum_eerste_toelating']) ? (int) substr($rdwData['datum_eerste_toelating'], 0, 4) : null,
                    'mileage' => 0, // RDW doesn't provide mileage, user needs to fill this
                    'color' => $rdwData['eerste_kleur'] ?? '',
                    'seats' => isset($rdwData['aantal_zitplaatsen']) ? (int)$rdwData['aantal_zitplaatsen'] : null,
                    'doors' => isset($rdwData['aantal_deuren']) ? (int)$rdwData['aantal_deuren'] : null,
                    'weight' => isset($rdwData['massa_ledig_voertuig']) ? (int)$rdwData['massa_ledig_voertuig'] : null,
                    'fuel_type' => $fuelData['brandstof_omschrijving'] ?? null,
                    'engine_capacity' => isset($rdwData['cilinderinhoud']) ? (int)$rdwData['cilinderinhoud'] : null,
                    'power_kw' => isset($rdwData['vermogen_massarijklaar']) ? (int)$rdwData['vermogen_massarijklaar'] : null,
                ];
                
                return view('cars.verify', [
                    'car' => $carData,
                ]);
            } else {
                return redirect()->route('cars.create')
                    ->withInput()
                    ->withErrors(['license_plate' => 'Geen voertuiginformatie gevonden voor dit kenteken.']);
            }
        } catch (\Exception $e) {
            return redirect()->route('cars.create')
                ->withInput()
                ->withErrors(['license_plate' => 'Er is een fout opgetreden bij het ophalen van de voertuiggegevens. Probeer het later opnieuw.']);
        }
    }
    
    /**
     * Store car data temporarily and proceed to tags selection.
     */
    public function store(Request $request)
    {
        Log::info('Car store method called');
        Log::debug('Request data:', $request->all());
        
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::error('User is not authenticated when trying to store a car');
            return redirect()->route('login')
                ->with('error', 'Je moet ingelogd zijn om een auto toe te voegen.');
        }
        
        $userId = Auth::id();
        Log::info('Authenticated user ID: ' . $userId);
        
        try {
            $validated = $request->validate([
                'license_plate' => 'required|string|max:10',
                'brand' => 'required|string|max:50',
                'model' => 'required|string|max:50',
                'price' => 'required|numeric|min:0',
                'mileage' => 'required|integer|min:0',
                'production_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
                'color' => 'nullable|string|max:30',
                'seats' => 'nullable|integer|min:1|max:10',
                'doors' => 'nullable|integer|min:1|max:6',
                'weight' => 'nullable|integer|min:0',
                'fuel_type' => 'nullable|string|max:30',
                'engine_capacity' => 'nullable|integer|min:0',
                'power_kw' => 'nullable|integer|min:0',
                'image' => 'nullable|image|max:2048',
                'user_id' => 'nullable|integer',
            ]);
            
            Log::info('Validation passed');
            
            // Store the image temporarily if provided
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('temp', 'public');
                Log::info('Temporary image stored at: ' . $imagePath);
                $validated['image'] = $imagePath;
            }
            
            // Store validated data in the session for next step
            $request->session()->put('car_temp_data', $validated);
            
            // Redirect to the tags selection page
            return redirect()->route('cars.select-tags');
                
        } catch (\Exception $e) {
            Log::error('Error validating car data: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Er is een fout opgetreden: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show the tags selection page (step 3).
     */
    public function selectTags(Request $request)
    {
        // Check if there's temporary car data in the session
        if (!$request->session()->has('car_temp_data')) {
            return redirect()->route('cars.create')
                ->with('error', 'Je moet eerst de autogegevens invullen.');
        }
        
        // Get temporary car data from session
        $carData = $request->session()->get('car_temp_data');
        
        // Get all available tags
        $allTags = \App\Models\Tag::orderBy('name')->get();
        
        return view('cars.select-tags', [
            'carData' => $carData,
            'allTags' => $allTags
        ]);
    }
    
    /**
     * Finalize the car creation with selected tags.
     */
    public function finalize(Request $request)
    {
        // Check if there's temporary car data in the session
        if (!$request->session()->has('car_temp_data')) {
            return redirect()->route('cars.create')
                ->with('error', 'Je moet eerst de autogegevens invullen.');
        }
        
        $userId = Auth::id();
        $carData = $request->session()->get('car_temp_data');
        $selectedTagIds = $request->tags ?? [];
        
        try {
            DB::beginTransaction();
            
            // Create the car with the stored data
            $car = new Car();
            $car->user_id = $userId;
            $car->license_plate = $carData['license_plate'];
            $car->brand = $carData['brand'];
            $car->model = $carData['model'];
            $car->price = $carData['price'];
            $car->mileage = $carData['mileage'];
            
            // Set nullable fields
            if (!empty($carData['production_year'])) {
                $car->production_year = $carData['production_year'];
            }
            if (!empty($carData['color'])) {
                $car->color = $carData['color'];
            }
            if (!empty($carData['seats'])) {
                $car->seats = $carData['seats'];
            }
            if (!empty($carData['doors'])) {
                $car->doors = $carData['doors'];
            }
            if (!empty($carData['weight'])) {
                $car->weight = $carData['weight'];
            }
            if (!empty($carData['fuel_type'])) {
                $car->fuel_type = $carData['fuel_type'];
            }
            if (!empty($carData['engine_capacity'])) {
                $car->engine_capacity = $carData['engine_capacity'];
            }
            if (!empty($carData['power_kw'])) {
                $car->power_kw = $carData['power_kw'];
            }
            
            // Default values
            $car->views = 0;
            
            // Handle the image
            if (!empty($carData['image'])) {
                // Move from temp to permanent storage if needed
                $tempPath = $carData['image'];
                $finalPath = 'cars/' . basename($tempPath);
                Storage::disk('public')->move($tempPath, $finalPath);
                $car->image = $finalPath;
            }
            
            $success = $car->save();
            
            if (!$success) {
                throw new \Exception('Failed to save car to the database.');
            }
            
            // Attach selected tags to the car
            if (!empty($selectedTagIds)) {
                $car->tags()->attach($selectedTagIds);
            }
            
            // Remove temporary data from the session
            $request->session()->forget('car_temp_data');
            
            DB::commit();
            Log::info('Car saved successfully with ID: ' . $car->id);
            
            return redirect()->route('cars.index')
                ->with('success', 'Auto succesvol toegevoegd!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving car: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['general' => 'Er is een fout opgetreden bij het opslaan van de auto: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Remove the specified car from storage.
     */
    public function destroy(Car $car)
    {
        // Check if the current user is the owner of the car
        if ($car->user_id !== Auth::id()) {
            return redirect()->route('cars.index')
                ->with('error', 'Je hebt geen toestemming om deze auto te verwijderen.');
        }
        
        // Delete the car image if it exists
        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }
        
        // Delete the car
        $car->delete();
        
        return redirect()->route('cars.index')
            ->with('success', 'Auto succesvol verwijderd!');
    }

    /**
     * Show detailed view statistics for a specific car.
     */
    public function viewStats(Car $car): View
    {
        // Check if the current user is the owner of the car
        if ($car->user_id !== Auth::id()) {
            return redirect()->route('cars.index')
                ->with('error', 'Je hebt geen toestemming om deze statistieken te bekijken.');
        }
        
        // Gather some additional statistics (deze zouden in een echte applicatie uit de database komen)
        $dailyViews = [
            date('Y-m-d', strtotime('-6 days')) => rand(0, 10),
            date('Y-m-d', strtotime('-5 days')) => rand(0, 15),
            date('Y-m-d', strtotime('-4 days')) => rand(0, 20),
            date('Y-m-d', strtotime('-3 days')) => rand(5, 25),
            date('Y-m-d', strtotime('-2 days')) => rand(5, 30),
            date('Y-m-d', strtotime('-1 days')) => rand(5, 35),
            date('Y-m-d') => rand(0, 40),
        ];
        
        // Simuleer vergelijkbare auto's en hun gemiddelde views
        $similarCarsAvgViews = rand($car->views * 0.7, $car->views * 1.3);
        
        // Populariteitsscore (0-100)
        $popularityScore = min(100, ($car->views / max(1, $similarCarsAvgViews)) * 70);
        
        // Simuleer potentiële kopers (25-35% van de views)
        $potentialBuyers = round($car->views * (rand(25, 35) / 100));
        
        return view('cars.view-stats', [
            'car' => $car,
            'dailyViews' => $dailyViews,
            'similarCarsAvgViews' => round($similarCarsAvgViews),
            'popularityScore' => round($popularityScore),
            'potentialBuyers' => $potentialBuyers,
            'viewsPercentage' => $car->views > 0 ? round(($potentialBuyers / $car->views) * 100) : 0
        ]);
    }
    
    /**
     * Update the car status to sold.
     */
    public function markAsSold(Car $car)
    {
        // Check if the current user is the owner of the car
        if ($car->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Je hebt geen toestemming om deze auto aan te passen.'
            ], 403);
        }
        
        // Update sold_at timestamp
        $car->sold_at = now();
        $car->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Auto gemarkeerd als verkocht.',
            'car_id' => $car->id,
            'sold_at' => $car->sold_at->format('d-m-Y H:i')
        ]);
    }
    
    /**
     * Update the car status to available.
     */
    public function markAsAvailable(Car $car)
    {
        // Check if the current user is the owner of the car
        if ($car->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Je hebt geen toestemming om deze auto aan te passen.'
            ], 403);
        }
        
        // Remove sold_at timestamp
        $car->sold_at = null;
        $car->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Auto gemarkeerd als beschikbaar.',
            'car_id' => $car->id
        ]);
    }
    
    /**
     * Update the car price.
     */
    public function updatePrice(Request $request, Car $car)
    {
        // Check if the current user is the owner of the car
        if ($car->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Je hebt geen toestemming om deze auto aan te passen.'
            ], 403);
        }
        
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);
        
        $oldPrice = $car->price;
        $car->price = $request->price;
        $car->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Prijs succesvol aangepast.',
            'car_id' => $car->id,
            'old_price' => $oldPrice,
            'new_price' => $car->price,
            'formatted_price' => '€' . number_format($car->price, 2, ',', '.')
        ]);
    }

    /**
     * Display all available cars to the public.
     */
    public function showPublicListing(Request $request): View
    {
        // Start with a base query for available cars
        $query = Car::with('tags')->whereNull('sold_at')->latest();
        
        // Apply search filters if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('brand', 'like', '%' . $searchTerm . '%')
                  ->orWhere('model', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Apply tag filters if provided
        if ($request->has('tags') && !empty($request->tags)) {
            $tagIds = is_array($request->tags) ? $request->tags : [$request->tags];
            
            // Filter cars that have all the selected tags
            $query->whereHas('tags', function($q) use ($tagIds) {
                $q->whereIn('tags.id', $tagIds);
            }, '=', count($tagIds));
        }
        
        // Paginate cars with 12 items per page
        $cars = $query->paginate(12);
        
        // Get all tags for the filter options
        $tags = \App\Models\Tag::orderBy('priority', 'desc')
            ->orderBy('name', 'asc')
            ->get();
        
        // Get the currently selected tag IDs
        $selectedTags = $request->tags ? (is_array($request->tags) ? $request->tags : [$request->tags]) : [];
        
        // Prepare cars for playful grid layout
        $featuredIndexes = [];
        if ($cars->count() > 0) {
            // Determine how many cars to feature based on current page count
            $numToFeature = min(max(1, floor($cars->count() * 0.3)), 3);
            
            // When using pagination, we only need to consider indexes of the current page
            $availableIndexes = range(0, $cars->count() - 1);
            
            // Randomly select indexes to feature
            if ($numToFeature == 1) {
                $featuredIndexes = [array_rand($availableIndexes, 1)];
            } elseif ($numToFeature > 1) {
                $featuredIndexes = (array) array_rand($availableIndexes, $numToFeature);
            }
        }
        
        // Check if this is an AJAX request (for live search)
        if ($request->ajax()) {
            return view('cars.partials.car-grid', [
                'cars' => $cars,
                'featuredIndexes' => $featuredIndexes,
                'tags' => $tags,
                'selectedTags' => $selectedTags
            ]);
        }
        
        return view('cars.public-listing', [
            'cars' => $cars,
            'featuredIndexes' => $featuredIndexes,
            'tags' => $tags,
            'selectedTags' => $selectedTags
        ]);
    }

    /**
     * Display the specified car to the public.
     */
    public function show(Car $car): View
    {
        // Increment the view count
        $car->increment('views');
        
        // In a real application, we would track daily views in a separate table
        // For now, let's simulate some daily views that's proportional to total views
        // but with some randomness to make it more realistic
        $dailyViews = min(ceil($car->views * 0.2), 1); // At least 1 view (the current view)
        if ($car->views > 10) {
            $dailyViews = rand(ceil($car->views * 0.1), ceil($car->views * 0.3));
        }
        
        // Calculate potential buyers (approximately 15-25% of daily views)
        $potentialBuyers = max(1, ceil($dailyViews * (rand(15, 25) / 100)));
        
        return view('cars.show', [
            'car' => $car,
            'dailyViews' => $dailyViews,
            'potentialBuyers' => $potentialBuyers
        ]);
    }

    /**
     * Show form to edit tags for a car.
     */
    public function editTags(Car $car): View
    {
        // Check if the current user is the owner of the car
        if ($car->user_id !== Auth::id()) {
            return redirect()->route('cars.index')
                ->with('error', 'Je hebt geen toestemming om deze auto aan te passen.');
        }
        
        // Get all available tags
        $allTags = \App\Models\Tag::orderBy('name')->get();
        
        // Get the IDs of the tags currently associated with the car
        $selectedTagIds = $car->tags->pluck('id')->toArray();
        
        return view('cars.edit-tags', [
            'car' => $car,
            'allTags' => $allTags,
            'selectedTagIds' => $selectedTagIds
        ]);
    }
    
    /**
     * Update tags for a car.
     */
    public function updateTags(Request $request, Car $car)
    {
        // Check if the current user is the owner of the car
        if ($car->user_id !== Auth::id()) {
            return redirect()->route('cars.index')
                ->with('error', 'Je hebt geen toestemming om deze auto aan te passen.');
        }
        
        // Get the selected tag IDs from the request (empty array if none selected)
        $selectedTagIds = $request->tags ?? [];
        
        // Sync the tags (this will add new tags and remove unchecked ones)
        $car->tags()->sync($selectedTagIds);
        
        return redirect()->route('cars.index')
            ->with('success', 'Tags succesvol bijgewerkt!');
    }
} 