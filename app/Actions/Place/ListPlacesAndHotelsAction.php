<?php

namespace App\Actions\Place;

use App\Models\Hotel;
use App\Models\Place;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Cache;

class ListPlacesAndHotelsAction
{
  use ApiTrait;

  public function execute()
  {
    try {
      $data = Cache::rememberForever('places.all_with_hotels', function () {
        $places = Place::all();
        $hotels = Hotel::all();

        if ($places->isEmpty() && $hotels->isEmpty()) {
          return null;
        }

        return [
          'allPlaces' => $places->map(fn ($place) => [
            'id' => $place->id,
            'name' => $place->name,
            'description' => $place->description,
            'address' => $place->address,
            'img_url' => $place->images->pluck('data'),
          ]),
          'allHotels' => $hotels->map(fn ($hotel) => [
            'id' => $hotel->id,
            'name' => $hotel->name,
            'address' => $hotel->address,
            'img_url' => $hotel->images->pluck('data'),
          ]),
        ];
      });

      if (!$data) {
        return ApiTrait::errorMessage([], 'No Places or Hotels Available', 404);
      }

      return ApiTrait::data($data, 'Places and hotels fetched', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Failed to load places', 500);
    }
  }
}
