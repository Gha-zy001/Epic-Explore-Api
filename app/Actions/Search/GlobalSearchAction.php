<?php

namespace App\Actions\Search;

use App\Models\Hotel;
use App\Models\Place;
use App\Models\Restaurant;
use App\Traits\ApiTrait;

class GlobalSearchAction
{
  use ApiTrait;

  public function execute(?string $query)
  {
    if (!$query) {
      return ApiTrait::errorMessage([], 'Search query is required', 400);
    }

    try {
      $places = Place::where('name', 'like', "%{$query}%")
        ->orWhere('description', 'like', "%{$query}%")
        ->with(['images'])
        ->get();

      $hotels = Hotel::where('name', 'like', "%{$query}%")
        ->orWhere('address', 'like', "%{$query}%")
        ->with(['images'])
        ->get();

      $restaurants = Restaurant::where('name', 'like', "%{$query}%")
        ->orWhere('address', 'like', "%{$query}%")
        ->with(['images'])
        ->get();

      return ApiTrait::data(
        compact('places', 'hotels', 'restaurants'),
        'Search results fetched successfully'
      );
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'An error occurred during search', 500);
    }
  }
}
