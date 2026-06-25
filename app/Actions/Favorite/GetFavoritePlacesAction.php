<?php

namespace App\Actions\Favorite;

use App\Http\Resources\FavoriteResource;
use App\Models\Place;
use App\Models\User;
use App\Traits\ApiTrait;

class GetFavoritePlacesAction
{
  use ApiTrait;

  public function execute(User $user)
  {
    try {
      $favorites = Place::whereHas('favorites', function ($query) use ($user) {
        $query->where('user_id', $user->id)
          ->where('favoritable_type', Place::class);
      })->with('favorites')->get();

      return FavoriteResource::collection($favorites);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'There is no favorites yet', 422);
    }
  }
}
