<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('resturaunts', 'restaurants');
        Schema::rename('resturant_images', 'restaurant_images');
        Schema::rename('img_trips', 'trip_images');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('restaurants', 'resturaunts');
        Schema::rename('restaurant_images', 'resturant_images');
        Schema::rename('trip_images', 'img_trips');
    }
};
