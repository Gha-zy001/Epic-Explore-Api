<?php

namespace App\Actions\Favorite;

use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Cache;

class GetFavoritesAction
{
  use ApiTrait;

  public function execute(User $user)
  {
    try {
      $favorites = Cache::remember(
        'favorites_' . $user->id,
        now()->addMinutes(30),
        fn () => Favorite::where('user_id', $user->id)->get()
      );

      return FavoriteResource::collection($favorites);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'There is no favorites yet', 422);
    }
  }
}
