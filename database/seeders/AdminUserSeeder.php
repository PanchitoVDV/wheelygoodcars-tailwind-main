<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Controleer of er al een admin gebruiker bestaat
        $adminExists = User::where('email', 'admin@wheelygoodcars.nl')->exists();
        
        if (!$adminExists) {
            // Maak een beheerder account aan
            User::create([
                'name' => 'Admin',
                'email' => 'admin@wheelygoodcars.nl',
                'password' => Hash::make('password'),
                'role' => 'beheerder',
            ]);
            
            $this->command->info('Beheerder account is aangemaakt!');
        } else {
            // Update bestaande admin gebruiker om ervoor te zorgen dat deze de juiste rol heeft
            $admin = User::where('email', 'admin@wheelygoodcars.nl')->first();
            $admin->role = 'beheerder';
            $admin->save();
            
            $this->command->info('Bestaand beheerder account bijgewerkt!');
        }
    }
} 