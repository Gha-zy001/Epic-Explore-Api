<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Traits\ApiTrait;
use App\Services\PlaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PlaceController extends Controller
{
  protected PlaceService $placeService;

  public function __construct(PlaceService $placeService)
  {
    $this->placeService = $placeService;
  }

  public function index()
  {
    try {
      $data = $this->placeService->getAllPlacesAndHotels();

      if (!$data) {
        return ApiTrait::errorMessage([], 'No Places or Hotels Available', 404);
      }

      return ApiTrait::data($data, 'Places and hotels fetched', 200);
    } catch (\Throwable $th) {
      Log::error('PlaceController@index failed', ['error' => $th->getMessage()]);
      return ApiTrait::errorMessage([], 'Failed to load places', 500);
    }
  }

  public function show($id)
  {
    try {
      $place = $this->placeService->getPlaceById((int) $id);
      if ($place) {
        $placeById = new PlaceResource($place);
        return ApiTrait::data(compact('placeById'));
      }
      return ApiTrait::errorMessage([], 'Place not found', 404);
    } catch (\Throwable $th) {
      Log::error('PlaceController@show failed', ['error' => $th->getMessage(), 'id' => $id]);
      return ApiTrait::errorMessage([], 'Failed to load place', 500);
    }
  }

  public function getPlacesByState(Request $request, $stateName)
  {
    try {
      $places = $this->placeService->getPlacesByState($stateName);

      if ($places === null) {
        return ApiTrait::errorMessage([], 'State not found', 404);
      }

      return ApiTrait::data(compact('places'), 'Places fetched by state', 200);
    } catch (\Throwable $th) {
      Log::error('PlaceController@getPlacesByState failed', [
        'error' => $th->getMessage(),
        'state' => $stateName,
      ]);
      return ApiTrait::errorMessage([], 'Failed to load places', 500);
    }
  }

  public function checkIn(Request $request)
  {
    $request->validate([
      'place_id' => 'required|exists:places,id',
      'latitude' => 'nullable|numeric',
      'longitude' => 'nullable|numeric',
    ]);

    try {
      $visit = $this->placeService->checkInAuthUser(
        (int) $request->place_id,
        $request->latitude,
        $request->longitude
      );

      return ApiTrait::data(['visit' => $visit], 'Check-in successful! +50 XP awarded.', 200);
    } catch (\Throwable $th) {
      Log::error('PlaceController@checkIn failed', [
        'error' => $th->getMessage(),
        'user_id' => auth()->id(),
        'place_id' => $request->place_id,
      ]);
      return ApiTrait::errorMessage([], 'Check-in failed', 500);
    }
  }
}
