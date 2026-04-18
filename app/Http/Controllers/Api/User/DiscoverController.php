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
        // Popular: Top 10 by review count (simulated by random for now, or just limit)
        $popular = Place::withCount('reviews')
            ->orderBy('reviews_count', 'desc')
            ->limit(5)
            ->get();

        // Hidden Gems: Less than 5 reviews but highly rated (simulated)
        $hiddenGems = Place::withCount('reviews')
            ->having('reviews_count', '<', 5)
            ->limit(5)
            ->get();

        // Nearby: Simulated for now
        $nearby = Place::inRandomOrder()->limit(5)->get();

        return ApiTrait::data([
            'popular' => $popular,
            'hidden_gems' => $hiddenGems,
            'nearby' => $nearby,
        ]);
    }
}
