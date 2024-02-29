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
    Schema::create('photos', function (Blueprint $table) {
      $table->id();
      $table->longText('data');
      // $table->foreignId('place_id')->constrained('pictures')->onDelete('cascade');
      // $table->foreignId('hotel_id')->constrained('pictures')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('pictures');
  }
};