<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'color',
        'priority',
        'is_featured',
    ];
    
    protected $casts = [
        'is_featured' => 'boolean',
    ];
    
    /**
     * The cars that belong to the tag.
     */
    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class);
    }
}
