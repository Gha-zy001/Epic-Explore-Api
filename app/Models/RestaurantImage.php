<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantImage extends Model
{
    use HasFactory;
    protected $fillable = [
      'data',
      'resturant_id'
    ];
  
    public function resturant()
    {
      return $this->belongsTo(Restaurant::class,'resturant_id');
    }
}
