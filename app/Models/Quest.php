<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quest extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'reward_xp',
        'requirement_type',
        'requirement_count'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_quests')
            ->withPivot('progress', 'status')
            ->withTimestamps();
    }
}
