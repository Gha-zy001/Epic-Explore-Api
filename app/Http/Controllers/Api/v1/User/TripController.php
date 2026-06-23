<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\TripResource;
use App\Traits\ApiTrait;
use App\Services\TripService;
use App\Http\Requests\StoreTripRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
      Log::error('TripController@getTrip failed', ['error' => $th->getMessage(), 'user_id' => auth()->id()]);
      return ApiTrait::errorMessage([], 'Failed to fetch trips', 500);
    }
  }

  public function specificTrip($tripId)
  {
    try {
      $trip = $this->tripService->getSpecificTrip(auth()->id(), (int) $tripId);
      $tripById = new TripResource($trip);
      return ApiTrait::data(compact('tripById'));
    } catch (NotFoundHttpException $e) {
      return ApiTrait::errorMessage([], 'Trip not found', 404);
    } catch (\Throwable $th) {
      Log::error('TripController@specificTrip failed', [
        'error' => $th->getMessage(),
        'user_id' => auth()->id(),
        'trip_id' => $tripId,
      ]);
      return ApiTrait::errorMessage([], 'Failed to fetch trip', 500);
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
      Log::error('TripController@createTrip failed', ['error' => $th->getMessage()]);
      return ApiTrait::errorMessage([], 'Failed to create trip', 500);
    }
  }

  public function uploadImages(Request $request, $tripId)
  {
    $request->validate([
      'images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    try {
      if ($request->hasFile('images')) {
        $count = $this->tripService->uploadTripImages(
          auth()->id(),
          (int) $tripId,
          $request->file('images')
        );
        return ApiTrait::data(['uploaded' => $count], 'Images uploaded successfully', 200);
      }
      return ApiTrait::errorMessage([], 'No images uploaded', 400);
    } catch (\RuntimeException $e) {
      return ApiTrait::errorMessage([], $e->getMessage(), 404);
    } catch (\Throwable $th) {
      Log::error('TripController@uploadImages failed', [
        'error' => $th->getMessage(),
        'user_id' => auth()->id(),
        'trip_id' => $tripId,
      ]);
      return ApiTrait::errorMessage([], 'Upload failed', 500);
    }
  }

  public function updateTrip(Request $request, $id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], 'Invalid Trip id', 400);
    }

    try {
      $trip = $this->tripService->updateTrip(auth()->id(), (int) $id, $request->validated() ?? $request->all());

      if (!$trip) {
        return ApiTrait::errorMessage([], 'Trip not found or access denied', 404);
      }

      return new TripResource($trip);
    } catch (\Throwable $th) {
      Log::error('TripController@updateTrip failed', [
        'error' => $th->getMessage(),
        'user_id' => auth()->id(),
        'trip_id' => $id,
      ]);
      return ApiTrait::errorMessage([], 'Failed to update trip', 500);
    }
  }

  public function deleteTrip($id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], 'Invalid Trip id', 400);
    }

    try {
      $success = $this->tripService->deleteTrip(auth()->id(), (int) $id);

      if (!$success) {
        return ApiTrait::errorMessage([], 'Trip not found or access denied', 404);
      }

      return ApiTrait::successMessage('Trip deleted', 200);
    } catch (\Throwable $th) {
      Log::error('TripController@deleteTrip failed', [
        'error' => $th->getMessage(),
        'user_id' => auth()->id(),
        'trip_id' => $id,
      ]);
      return ApiTrait::errorMessage([], 'Failed to delete trip', 500);
    }
  }
}
