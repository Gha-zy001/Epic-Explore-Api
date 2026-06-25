<?php

namespace App\Actions\Trip;

use App\Actions\Trip\Concerns\ManagesTripCache;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Traits\ApiTrait;

class ShowTripAction
{
  use ApiTrait;
  use ManagesTripCache;

  public function execute(int $userId, int $tripId)
  {
    try {
      $trip = $this->rememberTrip("user.{$userId}.trip.{$tripId}", function () use ($userId, $tripId) {
        return Trip::where('user_id', $userId)
          ->where('id', $tripId)
          ->firstOrFail();
      });

      return ApiTrait::data(['tripById' => new TripResource($trip)]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
      return ApiTrait::errorMessage([], 'Trip not found', 404);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Failed to fetch trip', 500);
    }
  }
}
