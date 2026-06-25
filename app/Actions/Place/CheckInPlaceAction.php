<?php

namespace App\Actions\Place;

use App\Models\Place;
use App\Models\User;
use App\Models\Visit;
use App\Services\PointService;
use App\Traits\ApiTrait;

class CheckInPlaceAction
{
  use ApiTrait;

  public function __construct(
    private PointService $pointService,
  ) {}

  public function execute(User $user, int $placeId, ?float $latitude = null, ?float $longitude = null)
  {
    try {
      $place = Place::findOrFail($placeId);

      $visit = Visit::create([
        'user_id' => $user->id,
        'place_id' => $placeId,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'points_awarded' => 50,
      ]);

      $this->pointService->awardExperience(
        $user,
        50,
        "Checked in to {$place->name}",
        'xp',
        'visits',
        $visit
      );

      return ApiTrait::data(['visit' => $visit], 'Check-in successful! +50 XP awarded.', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Check-in failed', 500);
    }
  }
}
