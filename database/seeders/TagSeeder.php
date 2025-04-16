<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Featured tags with higher priority
            ['name' => 'Elektrisch', 'color' => '#34D399', 'priority' => 9, 'is_featured' => true], // Green
            ['name' => 'Hybride', 'color' => '#60A5FA', 'priority' => 8, 'is_featured' => true], // Blue
            ['name' => 'Automaat', 'color' => '#8B5CF6', 'priority' => 7, 'is_featured' => true], // Purple
            ['name' => 'Navigatie', 'color' => '#F59E0B', 'priority' => 6, 'is_featured' => true], // Amber
            ['name' => 'Cabrio', 'color' => '#EC4899', 'priority' => 5, 'is_featured' => true], // Pink
            
            // Regular tags
            ['name' => 'Benzine', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false], // Gray
            ['name' => 'Diesel', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'LPG', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Handgeschakeld', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'SUV', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Sedan', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Hatchback', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Station', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Leder', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Trekhaak', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Panoramadak', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Cruise control', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Airco', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Achteruitrijcamera', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
            ['name' => 'Parkeersensoren', 'color' => '#6B7280', 'priority' => 0, 'is_featured' => false],
        ];
        
        foreach ($tags as $tagData) {
            $name = $tagData['name'];
            Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'color' => $tagData['color'],
                'priority' => $tagData['priority'],
                'is_featured' => $tagData['is_featured'],
            ]);
        }
    }
}
