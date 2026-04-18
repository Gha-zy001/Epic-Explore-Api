<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RewardLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reward_type',
        'description',
        'points',
        'reference_type',
        'reference_id',
        'meta'
    ];

    protected $casts = [
        'meta' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
