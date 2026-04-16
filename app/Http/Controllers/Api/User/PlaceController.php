<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlaceResource;
use App\Traits\ApiTrait;
use App\Services\PlaceService;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
  protected PlaceService $placeService;

  public function __construct(PlaceService $placeService)
  {
    $this->placeService = $placeService;
  }

  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    try {
      $data = $this->placeService->getAllPlacesAndHotels();
      
      if (!$data) {
        return ApiTrait::errorMessage([], 'No Places or Hotels Available', 422);
      }

      return $data;
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'An error occurred', 500);
    }
  }

  /**
   * Display the specified resource.
   */
  public function show($id)
  {
    try {
      $place = $this->placeService->getPlaceById($id);
      if ($place) {
        $placeById = new PlaceResource($place);
        return ApiTrait::data(compact('placeById'));
      }
      return ApiTrait::errorMessage([], 'Place not found', 404);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'An error occurred', 500);
    }
  }

  /**
   * Get places by state name.
   */
  public function getPlacesByState(Request $request, $stateName)
  {
    try {
      $places = $this->placeService->getPlacesByState($stateName);
      
      if ($places === null) {
          return response()->json(['error' => 'State not found'], 404);
      }

      return compact('places');
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'An error occurred', 422);
    }
  }
}

