<?php

namespace App\Services;

use App\Models\Place;
use App\Models\Hotel;
use App\Models\Restaurant;

class SearchService extends BaseService
{
    /**
     * Search across places, hotels, and restaurants.
     */
    public function globalSearch(string $query)
    {
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

        return [
            'places' => $places,
            'hotels' => $hotels,
            'restaurants' => $restaurants,
        ];
    }
}
