<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
  use HasFactory;
  protected $table = 'trips';
  protected $fillable = [
    'user_id',
    'name',
    'date',
    'city',
    'trip_id'
  ];
  protected $with = [
    'images',
  ];
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
  public function images()
  {
    return $this->hasMany(ImgTrip::class, 'trip_id');
  }
}
