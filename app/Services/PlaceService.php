<?php

namespace App\Services;

use App\Models\Place;
use App\Models\Hotel;
use App\Models\State;
use Illuminate\Support\Facades\Cache;

class PlaceService extends BaseService
{
    protected string $cachePrefix = 'places';

    /**
     * Get all places and hotels for home/index.
     */
    public function getAllPlacesAndHotels()
    {
        return $this->remember('all_with_hotels', function () {
            $places = Place::all();
            $hotels = Hotel::all();

            if ($places->isEmpty() && $hotels->isEmpty()) {
                return null;
            }

            return [
                'allPlaces' => $places->map(function ($place) {
                    return [
                        'id' => $place->id,
                        'name' => $place->name,
                        'description' => $place->description,
                        'address' => $place->address,
                        'img_url' => $place->images->pluck('data'),
                    ];
                }),
                'allHotels' => $hotels->map(function ($hotel) {
                    return [
                        'id' => $hotel->id,
                        'name' => $hotel->name,
                        'address' => $hotel->address,
                        'img_url' => $hotel->images->pluck('data'),
                    ];
                }),
            ];
        });
    }

    /**
     * Get place by ID.
     */
    public function getPlaceById(int $id)
    {
        return Place::find($id);
    }

    /**
     * Get places by state name with caching.
     */
    public function getPlacesByState(string $stateName)
    {
        return $this->remember("state.{$stateName}", function () use ($stateName) {
            $state = State::where('name', $stateName)->first();
            if (!$state) {
                return null;
            }

            return Place::where('state_id', $state->id)
                ->with(['images' => function ($query) {
                    $query->select('data', 'place_id');
                }])
                ->get(['id', 'state_id', 'name', 'description', 'address'])
                ->map(function ($place) {
                    return [
                        'id' => $place->id,
                        'state_id' => $place->state_id,
                        'name' => $place->name,
                        'description' => $place->description,
                        'address' => $place->address,
                        'img_url' => $place->images->pluck('data')->toArray(),
                    ];
                })->toArray();
        });
    }
}
