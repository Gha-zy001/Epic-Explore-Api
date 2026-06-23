<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DiscoverController extends Controller
{
    public function index(Request $request)
    {
        $popular = Cache::remember('discover.popular', 3600, function () {
            return Place::withCount('reviews')
                ->orderBy('reviews_count', 'desc')
                ->limit(5)
                ->get();
        });

        $hiddenGems = Cache::remember('discover.hidden_gems', 3600, function () {
            return Place::withCount('reviews')
                ->get()
                ->filter(fn($place) => $place->reviews_count < 5)
                ->take(5)
                ->values();
        });

        $nearby = Cache::remember('discover.nearby', 3600, function () {
            return Place::inRandomOrder()->limit(5)->get();
        });

        return ApiTrait::data([
            'popular' => $popular,
            'hidden_gems' => $hiddenGems,
            'nearby' => $nearby,
        ]);
    }
}
