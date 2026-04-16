<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Traits\ApiTrait;
use App\Services\TripService;
use App\Http\Requests\StoreTripRequest;
use Illuminate\Http\Request;

class TripController extends Controller
{
  protected TripService $tripService;

  public function __construct(TripService $tripService)
  {
    $this->tripService = $tripService;
  }

  public function getTrip()
  {
    try {
      $allTrips = $this->tripService->getUserTrips(auth()->id());
      $trips = TripResource::collection($allTrips);
      return ApiTrait::data(compact('trips'), 'Trips fetched successfully', 200);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  public function specificTrip($tripId)
  {
    try {
      $trip = $this->tripService->getSpecificTrip(auth()->id(), $tripId);
      if ($trip) {
        $tripById = new TripResource($trip);
        return ApiTrait::data(compact('tripById'));
      }
      return ApiTrait::errorMessage([], 'Trip not found', 404);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'An error occurred', 500);
    }
  }

  public function createTrip(StoreTripRequest $request)
  {
    try {
      $this->tripService->createTrip(array_merge($request->validated(), [
        'user_id' => auth()->id()
      ]));

      return ApiTrait::successMessage('Trip created successfully', 200);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  public function uploadImages(Request $request, $tripId)
  {
    $request->validate([
      'images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    try {
      if ($request->hasFile('images')) {
        $success = $this->tripService->uploadTripImages($tripId, $request->file('images'));
        if ($success) {
            return response()->json(['message' => 'Images uploaded successfully'], 200);
        }
      }
      return response()->json(['message' => 'No images uploaded'], 400);
    } catch (\Throwable $th) {
        return ApiTrait::errorMessage([], 'Upload failed', 500);
    }
  }

  public function updateTrip(Request $request, $id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], 'Invalid Trip id', 400);
    }

    try {
      $trip = $this->tripService->updateTrip($id, $request->all());

      if (!$trip) {
        return ApiTrait::errorMessage([], 'Trip not found', 404);
      }

      return new TripResource($trip);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  public function deleteTrip($id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], 'Invalid Trip id', 400);
    }

    try {
      $success = $this->tripService->deleteTrip($id);

      if (!$success) {
        return ApiTrait::errorMessage([], 'Trip not found or could not be deleted', 404);
      }

      return ApiTrait::successMessage('Trip deleted', 200);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }
}

