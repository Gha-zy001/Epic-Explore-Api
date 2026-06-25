<?php

namespace App\Actions\Favorite\Concerns;

use App\Models\Favorite;
use Illuminate\Support\Facades\Cache;

trait ManagesFavoriteCache
{
  protected function refreshFavoriteCache(int $userId): void
  {
    Cache::forget('favorites_' . $userId);
    Cache::forget("user.{$userId}.favorites");

    $favorites = Favorite::where('user_id', $userId)->get();
    Cache::put('favorites_' . $userId, $favorites, now()->addMinutes(30));
  }
}
