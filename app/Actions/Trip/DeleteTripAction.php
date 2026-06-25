<?php

namespace App\Actions\Trip;

use App\Actions\Trip\Concerns\ManagesTripCache;
use App\Models\Trip;
use App\Traits\ApiTrait;

class DeleteTripAction
{
  use ApiTrait;
  use ManagesTripCache;

  public function execute(int $userId, int $tripId)
  {
    if (!is_numeric($tripId)) {
      return ApiTrait::errorMessage([], 'Invalid Trip id', 400);
    }

    try {
      $trip = Trip::where('user_id', $userId)->where('id', $tripId)->first();

      if (!$trip) {
        return ApiTrait::errorMessage([], 'Trip not found or access denied', 404);
      }

      $trip->delete();
      $this->forgetUserTrips($userId, $tripId);

      return ApiTrait::successMessage('Trip deleted', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Failed to delete trip', 500);
    }
  }
}
