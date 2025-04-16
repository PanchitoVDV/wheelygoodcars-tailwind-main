<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class, // Eerst beheerder account aanmaken
            UserSeeder::class,      // Dan normale gebruikers
            TagSeeder::class,        // Dan tags
            CarSeeder::class,        // En tenslotte auto's met gebruiker en tag relaties
        ]);
    }
}
