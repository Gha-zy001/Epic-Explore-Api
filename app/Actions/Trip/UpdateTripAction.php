<?php

namespace App\Actions\Trip;

use App\Actions\Trip\Concerns\ManagesTripCache;
use App\Http\Resources\TripResource;
use App\Models\Trip;
use App\Traits\ApiTrait;

class UpdateTripAction
{
  use ApiTrait;
  use ManagesTripCache;

  public function execute(int $userId, int $tripId, array $data)
  {
    if (!is_numeric($tripId)) {
      return ApiTrait::errorMessage([], 'Invalid Trip id', 400);
    }

    try {
      $trip = Trip::where('user_id', $userId)->where('id', $tripId)->first();

      if (!$trip) {
        return ApiTrait::errorMessage([], 'Trip not found or access denied', 404);
      }

      $trip->update($data);
      $this->forgetUserTrips($userId, $tripId);

      return new TripResource($trip);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Failed to update trip', 500);
    }
  }
}
