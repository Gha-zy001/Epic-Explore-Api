<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImgTrip extends Model
{
    use HasFactory;

    protected $table = 'img_trips';
    protected $fillable = [
        'trip_id',
        'data',
    ];
}