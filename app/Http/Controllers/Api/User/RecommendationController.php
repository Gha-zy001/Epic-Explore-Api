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
      
      // Try AI Recommendations first
      $aiService = app(AiRecommendationService::class);
      $aiRecommendations = $aiService->getRecommendations($user);
      
      if ($aiRecommendations && $aiRecommendations->isNotEmpty()) {
          $recommendedData = PlaceResource::collection($aiRecommendations);
          return ApiTrait::data(compact('recommendedData'), 'Personalized AI recommendations', 200);
      }

      // Fallback to legacy logic
      $favoritePlaces = $user->favorites()
        ->where('favoritable_type', Place::class)
        ->get();

      if ($favoritePlaces->isNotEmpty()) {
        $latestFavoritePlace = $favoritePlaces->last()->favoritable;
        $state = $latestFavoritePlace->state;

        $recommendations = Place::where('state_id', $state->id)
          ->whereNotIn('id', $favoritePlaces->pluck('favoritable_id')->toArray())
          ->get()
          ->random(10);
        $recommendedData = PlaceResource::collection($recommendations);

        return ApiTrait::data(compact('recommendedData'), '', 200);
      } else {
        $places = Place::all()->random(10);
        $recommendedData = PlaceResource::collection($places);
        return ApiTrait::data(compact('recommendedData'), '', 200);
      }
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], '', 404);
    }
  }
}
