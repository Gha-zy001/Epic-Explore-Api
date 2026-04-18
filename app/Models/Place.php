<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Place extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'state_id',
    'place_id',
    'description',
    'address'
  ];
  protected $with = ['images'];

  public function favorites()
  {
    return $this->morphMany(Favorite::class, 'favoritable');
  }

  public function images()
  {
    return $this->hasMany(Image::class, 'place_id');
  }

  public function state()
  {
    return $this->belongsTo(State::class, 'state_id');
  }

  public function reviews()
  {
    return $this->morphMany(Review::class, 'reviewable');
  }

  public function visits()
  {
    return $this->hasMany(Visit::class);
  }
}
