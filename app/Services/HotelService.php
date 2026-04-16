<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\State;
use Illuminate\Support\Facades\Cache;

class HotelService extends BaseService
{
    protected string $cachePrefix = 'hotels';

    /**
     * Get all hotels with pagination.
     */
    public function getAllHotels(int $perPage = 10)
    {
        return Hotel::paginate($perPage);
    }

    /**
     * Get hotel by ID.
     */
    public function getHotelById(int $id)
    {
        return Hotel::find($id);
    }

    /**
     * Get hotels by state name with caching.
     */
    public function getHotelsByState(string $stateName)
    {
        return $this->remember("state.{$stateName}", function () use ($stateName) {
            $state = State::where('name', $stateName)->first();
            if (!$state) {
                return null;
            }

            return Hotel::where('state_id', $state->id)
                ->with(['images' => function ($query) {
                    $query->select('data', 'hotel_id');
                }])
                ->get(['id', 'state_id', 'name', 'rate', 'address', 'price'])
                ->map(function ($hotel) {
                    return [
                        'id' => $hotel->id,
                        'state_id' => $hotel->state_id,
                        'name' => $hotel->name,
                        'address' => $hotel->address,
                        'img_url' => $hotel->images->pluck('data')->toArray(),
                        'rate' => $hotel->rate,
                        'price' => $hotel->price,
                    ];
                })->toArray();
        });
    }
}
