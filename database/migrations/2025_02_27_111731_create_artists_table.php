<?php

// database/migrations/xxxx_xx_xx_create_artists_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArtistsTable extends Migration
{
    public function up()
    {
        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Artist name, should be unique
            $table->integer('subscribers_count')->default(0); // Subscribers count, default 0
            $table->integer('monthly_listeners')->default(0); // Monthly listeners count, default 0
            $table->integer('albums_count')->default(0); // Albums count, default 0
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('artists'); // Drop the 'artists' table if it exists
    }
}