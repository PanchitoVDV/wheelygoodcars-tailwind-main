<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We maken de admin al aan in de AdminUserSeeder

        // Maak 100 aanbieders
        for ($i = 1; $i <= 100; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => 'aanbieder' . $i . '@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'aanbieder',
            ]);
        }
        
        // Maak 50 kopers
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => fake()->name(),
                'email' => 'koper' . $i . '@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'koper',
            ]);
        }
    }
}
