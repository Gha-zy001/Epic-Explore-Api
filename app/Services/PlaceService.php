<?php

namespace App\Services;

use App\Models\Place;
use App\Models\Hotel;
use App\Models\State;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class PlaceService extends BaseService
{
    protected string $cachePrefix = 'places';
    protected PointService $pointService;

    public function __construct(PointService $pointService)
    {
        $this->pointService = $pointService;
    }

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

    /**
     * Check-in the authenticated user to a place.
     */
    public function checkInAuthUser(int $placeId, ?float $lat = null, ?float $lng = null)
    {
        return $this->checkIn(Auth::user(), $placeId, $lat, $lng);
    }

    /**
     * Check-in a user to a place.
     */
    public function checkIn(User $user, int $placeId, ?float $lat = null, ?float $lng = null)
    {
        $place = Place::findOrFail($placeId);

        // Record the visit
        $visit = Visit::create([
            'user_id' => $user->id,
            'place_id' => $placeId,
            'latitude' => $lat,
            'longitude' => $lng,
            'points_awarded' => 50, // Standard 50 XP for check-in
        ]);

        // Award XP
        $this->pointService->awardExperience($user, 50, "Checked in to {$place->name}", 'xp', $visit);

        return $visit;
    }
}
