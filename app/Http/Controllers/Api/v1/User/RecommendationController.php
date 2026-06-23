<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Place;
use App\Traits\ApiTrait;
use App\Http\Resources\PlaceResource;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\AiRecommendationService;

class RecommendationController extends Controller
{
  public function recommendPlaces()
  {
    try {
      $user = User::find(Auth::user()->id);
      
      $aiService = app(AiRecommendationService::class);
      $aiRecommendations = Cache::remember("recommendations.user.{$user->id}.ai", 3600, function () use ($aiService, $user) {
          return $aiService->getRecommendations($user);
      });
      
      if ($aiRecommendations && $aiRecommendations->isNotEmpty()) {
          $recommendedData = PlaceResource::collection($aiRecommendations);
          return ApiTrait::data(compact('recommendedData'), 'Personalized AI recommendations', 200);
      }

      $favoritePlaces = Cache::remember("user.{$user->id}.favorites", 3600, function () use ($user) {
          return $user->favorites()
            ->where('favoritable_type', Place::class)
            ->get();
      });

      if ($favoritePlaces->isNotEmpty()) {
        $latestFavoritePlace = $favoritePlaces->last()->favoritable;
        $state = $latestFavoritePlace->state;

        $recommendations = Cache::remember("recommendations.state.{$state->id}.user.{$user->id}", 3600, function () use ($state, $favoritePlaces) {
            return Place::where('state_id', $state->id)
              ->whereNotIn('id', $favoritePlaces->pluck('favoritable_id')->toArray())
              ->get()
              ->random(10);
        });
        
        $recommendedData = PlaceResource::collection($recommendations);

        return ApiTrait::data(compact('recommendedData'), '', 200);
      } else {
        $places = Cache::remember('recommendations.random.10', 3600, function () {
            return Place::all()->random(10);
        });

        $recommendedData = PlaceResource::collection($places);
        return ApiTrait::data(compact('recommendedData'), '', 200);
      }
    } catch (\Throwable $th) {
      return response()->json(['error' => $th->getMessage(), 'trace' => $th->getTraceAsString()], 500);
    }
  }
}
