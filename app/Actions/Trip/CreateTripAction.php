<?php

namespace App\Actions\Trip;

use App\Actions\Trip\Concerns\ManagesTripCache;
use App\Models\Trip;
use App\Services\PointService;
use App\Traits\ApiTrait;

class CreateTripAction
{
  use ApiTrait;
  use ManagesTripCache;

  public function __construct(
    private PointService $pointService,
  ) {}

  public function execute(array $data)
  {
    try {
      $trip = Trip::create($data);

      $this->forgetUserTrips($data['user_id']);

      $this->pointService->awardExperience(
        $trip->user,
        100,
        "Created trip: {$trip->title}",
        'xp',
        'trips',
        $trip
      );

      return ApiTrait::successMessage('Trip created successfully', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Failed to create trip', 500);
    }
  }
}
