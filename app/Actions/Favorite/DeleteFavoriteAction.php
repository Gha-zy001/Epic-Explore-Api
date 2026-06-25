<?php

namespace App\Actions\Favorite;

use App\Actions\Favorite\Concerns\ManagesFavoriteCache;
use App\Models\Favorite;
use App\Models\User;
use App\Traits\ApiTrait;

class DeleteFavoriteAction
{
  use ApiTrait;
  use ManagesFavoriteCache;

  public function execute(User $user, int $favoritableId)
  {
    try {
      Favorite::where('user_id', $user->id)
        ->where('favoritable_id', $favoritableId)
        ->delete();

      $this->refreshFavoriteCache($user->id);

      return ApiTrait::successMessage('Successfully deleted', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Fail', 422);
    }
  }
}
