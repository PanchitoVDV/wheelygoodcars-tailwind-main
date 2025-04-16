<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Console\Command;

class CheckSeedStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-seed-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the status of the seeded data (users, cars, tags)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking seed status...');
        
        // Count entities
        $userCount = User::count();
        $carCount = Car::count();
        $tagCount = Tag::count();
        
        // Display counts with colored output
        $this->newLine();
        $this->line('Database Seed Status:');
        $this->line('--------------------');
        $this->line("Users: <fg=green>{$userCount}</> (Target: 151)");
        $this->line("Cars: <fg=green>{$carCount}</> (Target: 250)");
        $this->line("Tags: <fg=green>{$tagCount}</> (Target: 20)");
        $this->newLine();
        
        // Get detailed tag information
        $this->line('Tag Details:');
        $this->line('------------');
        $tags = Tag::orderBy('priority', 'desc')->get();
        $headers = ['ID', 'Name', 'Priority', 'Featured', 'Color', 'Used Count'];
        $rows = [];
        
        foreach ($tags as $tag) {
            $rows[] = [
                $tag->id,
                $tag->name,
                $tag->priority,
                $tag->is_featured ? 'Yes' : 'No',
                $tag->color,
                $tag->cars()->count()
            ];
        }
        
        $this->table($headers, $rows);
        $this->newLine();
        
        // Get top 5 cars with most views
        $this->line('Top 5 Cars by Views:');
        $this->line('------------------');
        $topCars = Car::orderBy('views', 'desc')->take(5)->get();
        $carHeaders = ['ID', 'Brand', 'Model', 'Price', 'Views', 'Tags'];
        $carRows = [];
        
        foreach ($topCars as $car) {
            $carRows[] = [
                $car->id,
                $car->brand,
                $car->model,
                '€ ' . number_format($car->price, 2, ',', '.'),
                $car->views,
                $car->tags->pluck('name')->implode(', ')
            ];
        }
        
        $this->table($carHeaders, $carRows);
        
        return self::SUCCESS;
    }
}
