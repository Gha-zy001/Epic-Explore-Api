<?php

namespace App\Actions\Favorite;

use App\Http\Resources\FavoriteResource;
use App\Models\Hotel;
use App\Models\User;
use App\Traits\ApiTrait;

class GetFavoriteHotelsAction
{
  use ApiTrait;

  public function execute(User $user)
  {
    try {
      $favorites = Hotel::whereHas('favorites', function ($query) use ($user) {
        $query->where('user_id', $user->id)
          ->where('favoritable_type', Hotel::class);
      })->with('favorites')->get();

      return FavoriteResource::collection($favorites);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'There is no favorites yet', 422);
    }
  }
}
