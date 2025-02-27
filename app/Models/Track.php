<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    use HasFactory;

    // Fillable attributes to protect against mass-assignment vulnerability
    protected $fillable = [
        'name', 
        'duration', 
        'artist_id'
    ];

    /**
     * Many-to-one relationship: Each track belongs to one artist.
     * This defines the relationship with the Artist model.
     */
    public function artist()
    {
        return $this->belongsTo(Artist::class); // Each track belongs to one artist
    }
}