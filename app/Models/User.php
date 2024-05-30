<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
    'image',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  protected $appends = ['image_url'];

  public function favorites()
  {
    return $this->hasMany(Favorite::class);
  }

  public function reviews()
  {
    return $this->hasMany(Review::class);
  }

  public function trips()
  {
    return $this->hasMany(Trip::class);
  }

  public function places()
  {
    return $this->morphedByMany(Place::class, 'favoritable', 'favorites');
  }

  public function hotels()
  {
    return $this->morphedByMany(Hotel::class, 'favoritable', 'favorites');
  }

  public function getImageUrlAttribute()
  {
    return env('APP_URL') . '/storage/images/' . $this->image;
  }

  // public function sendEmailResetNotification($token)
  // {
  //   $url = 'https://spa.test/reset-password?token=' . $token;
  //   $this->notify(new ResetPasswordNotification($url));
  // }
}
