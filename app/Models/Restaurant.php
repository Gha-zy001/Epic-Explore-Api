<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
  use HasFactory;
  protected $fillable = [
    'resturant_id',
    'name',
    'rate',
    'address',
    'state_id'
  ];

  public function images()
  {
    return $this->hasMany(RestaurantImage::class, 'resturant_id');
  }
  public function state()
  {
    return $this->belongsTo(State::class, 'state_id');
  }
}
