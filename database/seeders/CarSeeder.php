<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users except the admin (user id 1)
        $users = User::where('id', '>', 1)->get();
        
        // Get all tags
        $tags = Tag::all();
        
        // Car brands and models
        $carBrands = [
            'Volkswagen' => ['Golf', 'Polo', 'Passat', 'Tiguan', 'ID.3', 'ID.4'],
            'Toyota' => ['Corolla', 'Yaris', 'RAV4', 'Camry', 'Prius', 'C-HR'],
            'Ford' => ['Focus', 'Fiesta', 'Mustang', 'Kuga', 'Puma', 'Transit'],
            'BMW' => ['3 Serie', '5 Serie', 'X3', 'X5', 'i3', '1 Serie'],
            'Mercedes' => ['A-Klasse', 'C-Klasse', 'E-Klasse', 'GLC', 'GLE', 'S-Klasse'],
            'Audi' => ['A3', 'A4', 'Q3', 'Q5', 'e-tron', 'A6'],
            'Renault' => ['Clio', 'Captur', 'Megane', 'Kadjar', 'Zoe', 'Talisman'],
            'Peugeot' => ['208', '2008', '308', '3008', '508', 'e-208'],
            'Kia' => ['Picanto', 'Rio', 'Ceed', 'Sportage', 'Niro', 'Stonic'],
            'Hyundai' => ['i10', 'i20', 'i30', 'Kona', 'Tucson', 'Ioniq']
        ];
        
        // Colors
        $colors = ['Zwart', 'Wit', 'Grijs', 'Zilver', 'Blauw', 'Rood', 'Groen', 'Bruin', 'Beige', 'Oranje'];
        
        // Fuel types
        $fuelTypes = ['Benzine', 'Diesel', 'Elektrisch', 'Hybride', 'LPG'];
        
        // Generate 250 cars
        for ($i = 1; $i <= 250; $i++) {
            // Random user, brand, model, and other attributes
            $user = $users->random();
            $brand = array_rand($carBrands);
            $model = $carBrands[$brand][array_rand($carBrands[$brand])];
            $fuelType = $fuelTypes[array_rand($fuelTypes)];
            
            $licensePlate = strtoupper(
                fake()->randomLetter() . fake()->randomLetter() . '-' .
                fake()->numberBetween(10, 99) . '-' .
                fake()->randomLetter() . fake()->randomLetter()
            );
            
            $car = Car::create([
                'user_id' => $user->id,
                'license_plate' => $licensePlate,
                'brand' => $brand,
                'model' => $model,
                'price' => fake()->numberBetween(5000, 75000),
                'mileage' => fake()->numberBetween(0, 250000),
                'seats' => fake()->numberBetween(2, 7),
                'doors' => fake()->numberBetween(2, 5),
                'production_year' => fake()->numberBetween(2010, 2023),
                'weight' => fake()->numberBetween(900, 2500),
                'color' => $colors[array_rand($colors)],
                'fuel_type' => $fuelType,
                'engine_capacity' => $fuelType !== 'Elektrisch' ? fake()->numberBetween(1000, 3000) : null,
                'power_kw' => fake()->numberBetween(50, 300),
                'views' => fake()->numberBetween(0, 1000),
                'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            ]);
            
            // Add 2-5 random tags to each car
            $randomTags = $tags->random(fake()->numberBetween(2, 5));
            $car->tags()->attach($randomTags);
        }
    }
}
