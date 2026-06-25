<?php

namespace App\Actions\Favorite;

use App\Actions\Favorite\Concerns\ManagesFavoriteCache;
use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Place;
use App\Models\User;
use App\Services\PointService;
use App\Traits\ApiTrait;

class AddFavoriteAction
{
  use ApiTrait;
  use ManagesFavoriteCache;

  public function __construct(
    private PointService $pointService,
  ) {}

  public function execute(User $user, string $favoritableType, int $favoritableId)
  {
    try {
      $favoritable = match ($favoritableType) {
        'place' => Place::findOrFail($favoritableId),
        'hotel' => Hotel::findOrFail($favoritableId),
        default => null,
      };

      if ($favoritable === null) {
        return ApiTrait::errorMessage([], 'Invalid favoritable type', 422);
      }

      $favorite = Favorite::create([
        'user_id' => $user->id,
        'favoritable_id' => $favoritableId,
        'favoritable_type' => $favoritable::class,
      ]);

      $this->refreshFavoriteCache($user->id);

      $this->pointService->awardExperience(
        $user,
        10,
        "Added {$favoritable->name} to favorites",
        'xp',
        'favorites',
        $favorite
      );

      return ApiTrait::successMessage('Successfully added to favorites', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Fail', 422);
    }
  }
}
