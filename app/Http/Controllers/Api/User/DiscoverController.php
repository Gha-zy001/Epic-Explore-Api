<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    public function index(Request $request)
    {
        // Popular: Top 10 by review count
        $popular = Place::withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->limit(5)
            ->get();

        // Hidden Gems: Less than 5 reviews - filter in PHP for SQLite compatibility
        $hiddenGems = Place::withCount('reviews')
            ->get()
            ->filter(fn($place) => $place->reviews_count < 5)
            ->take(5)
            ->values();

        // Nearby: Simulated for now
        $nearby = Place::inRandomOrder()->limit(5)->get();

        return ApiTrait::data([
            'popular' => $popular,
            'hidden_gems' => $hiddenGems,
            'nearby' => $nearby,
        ]);
    }
}
