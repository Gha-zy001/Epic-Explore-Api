<?php

namespace App\Http\Controllers;

use App\Http\Resources\RestaurantResource;
use App\Traits\ApiTrait;
use App\Services\RestaurantService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
  protected RestaurantService $restaurantService;

  public function __construct(RestaurantService $restaurantService)
  {
    $this->restaurantService = $restaurantService;
  }

  public function getRestaurants()
  {
    try {
      $restaurants = $this->restaurantService->getAllRestaurants(10);
      if ($restaurants->count() > 0) {
        $allRestaurants = RestaurantResource::collection($restaurants);
        return ApiTrait::data(compact('allRestaurants'), 'Restaurants Fetched Successfully', 200);
      }
      return ApiTrait::errorMessage([], 'No Restaurants Yet', 404);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  /**
   * Get restaurant by ID.
   */
  public function getRestaurant($id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], 'Invalid Restaurant Id', 400);
    }
    try {
      $restaurant = $this->restaurantService->getRestaurantById($id);
      if (!$restaurant) {
        return ApiTrait::errorMessage([], 'Restaurant Not Found', 404);
      }
      return RestaurantResource::collection([$restaurant]);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  /**
   * Get restaurants by state name.
   */
  public function getRestaurantsByState($stateName)
  {
    try {
      $restaurants = $this->restaurantService->getRestaurantsByState($stateName);
      
      if ($restaurants === null) {
          return response()->json(['error' => 'State not found'], 404);
      }

      return compact('restaurants');
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'An error occurred', 422);
    }
  }
}

