<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupRoles extends Command
{
    protected $signature = 'app:setup-roles';
    protected $description = 'Set up user roles by running migrations and seeding';

    public function handle()
    {
        $this->info('Setting up user roles...');
        
        $this->info('Running migrations...');
        Artisan::call('migrate');
        $this->info('Migrations completed');
        
        $this->info('Running seeders...');
        $adminExists = \App\Models\User::where('email', 'admin@wheelygoodcars.nl')->exists();
        Artisan::call('db:seed', ['--class' => 'AdminUserSeeder']);
        $this->info('Seeders completed');
        
        $this->info('User roles setup completed!');
        if ($adminExists) {
            $this->info('Admin user updated with role: beheerder');
        } else {
            $this->info('Admin user created with email: admin@wheelygoodcars.nl and password: password');
        }
        
        return Command::SUCCESS;
    }
} 