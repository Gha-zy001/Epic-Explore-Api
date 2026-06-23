<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\HotelResource;
use App\Traits\ApiTrait;
use App\Services\HotelService;
use Illuminate\Http\Request;

class HotelController extends Controller
{
  protected HotelService $hotelService;

  public function __construct(HotelService $hotelService)
  {
    $this->hotelService = $hotelService;
  }

  public function getHotels()
  {
    try {
      $hotels = $this->hotelService->getAllHotels(10);
      if ($hotels->count() > 0) {
        $allHotels = HotelResource::collection($hotels);
        return ApiTrait::data(compact('allHotels'), 'Hotels Fetched Successfully', 200);
      }
      return ApiTrait::errorMessage([], 'No Hotels Yet', 404);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  /**
   * Get hotel by ID.
   */
  public function getHotel($id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], 'Invalid Hotel Id', 400);
    }
    try {
      $hotel = $this->hotelService->getHotelById($id);
      if (!$hotel) {
        return ApiTrait::errorMessage([], 'Hotel Not Found', 404);
      }
      return HotelResource::collection([$hotel]);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  /**
   * Get hotels by state name.
   */
  public function getHotelsByState($stateName)
  {
    try {
      $hotels = $this->hotelService->getHotelsByState($stateName);
      
      if ($hotels === null) {
          return response()->json(['error' => 'State not found'], 404);
      }

      return compact('hotels');
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'An error occurred', 422);
    }
  }
}

