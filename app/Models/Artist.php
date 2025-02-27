<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    // Fillable attributes to protect against mass-assignment vulnerability
    protected $fillable = [
        'name', 
        'subscribers_count', 
        'monthly_listeners', 
        'albums_count'
    ];

    /**
     * One-to-many relationship: An artist can have many tracks.
     * This defines the relationship with the Track model.
     */
    public function tracks()
    {
        return $this->hasMany(Track::class); // Each artist has many tracks
    }
}