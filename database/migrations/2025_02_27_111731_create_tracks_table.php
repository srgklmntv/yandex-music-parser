<?php

// database/migrations/xxxx_xx_xx_create_tracks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTracksTable extends Migration
{
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id(); // Auto-increment ID
            $table->string('name'); // Track name
            $table->integer('duration'); // Track duration in seconds
            $table->foreignId('artist_id')->constrained()->onDelete('cascade'); // Foreign key to 'artists' table
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracks'); // Drop the 'tracks' table if it exists
    }
}