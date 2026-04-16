<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\State;
use Illuminate\Support\Facades\Cache;

class RestaurantService extends BaseService
{
    protected string $cachePrefix = 'restaurants';

    /**
     * Get all restaurants with pagination.
     */
    public function getAllRestaurants(int $perPage = 10)
    {
        return Restaurant::paginate($perPage);
    }

    /**
     * Get restaurant by ID.
     */
    public function getRestaurantById(int $id)
    {
        return Restaurant::find($id);
    }

    /**
     * Get restaurants by state name with caching.
     */
    public function getRestaurantsByState(string $stateName)
    {
        return $this->remember("state.{$stateName}", function () use ($stateName) {
            $state = State::where('name', $stateName)->first();
            if (!$state) {
                return null;
            }

            return Restaurant::where('state_id', $state->id)
                ->with(['images' => function ($query) {
                    $query->select('data', 'resturant_id');
                }])
                ->get(['id', 'state_id', 'name', 'rate', 'address'])
                ->map(function ($restaurant) {
                    return [
                        'id' => $restaurant->id,
                        'state_id' => $restaurant->state_id,
                        'name' => $restaurant->name,
                        'rate' => $restaurant->rate,
                        'address' => $restaurant->address,
                        'img_url' => $restaurant->images->pluck('data'),
                    ];
                })->toArray();
        });
    }
}
