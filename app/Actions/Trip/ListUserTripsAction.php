<?php

namespace App\Actions\Trip;

use App\Actions\Trip\Concerns\ManagesTripCache;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Traits\ApiTrait;

class ListUserTripsAction
{
  use ApiTrait;
  use ManagesTripCache;

  public function execute(int $userId)
  {
    try {
      $allTrips = $this->rememberTrip("user.{$userId}.all", function () use ($userId) {
        return Trip::where('user_id', $userId)->get();
      });

      return ApiTrait::data(
        ['trips' => TripResource::collection($allTrips)],
        'Trips fetched successfully',
        200
      );
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Failed to fetch trips', 500);
    }
  }
}
