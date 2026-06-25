<?php

namespace App\Actions\Recommendation;

use App\Http\Resources\PlaceResource;
use App\Models\Place;
use App\Models\User;
use App\Services\AiRecommendationService;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Cache;

class GetRecommendedPlacesAction
{
  use ApiTrait;

  public function __construct(
    private AiRecommendationService $aiService,
  ) {}

  public function resolve(User $user): array
  {
    $aiRecommendations = Cache::remember("recommendations.user.{$user->id}.ai", 3600, function () use ($user) {
      return $this->aiService->getRecommendations($user);
    });

    if ($aiRecommendations && $aiRecommendations->isNotEmpty()) {
      return [
        'recommendedData' => PlaceResource::collection($aiRecommendations),
        'message' => 'Personalized AI recommendations',
      ];
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
        $places = Place::where('state_id', $state->id)
          ->whereNotIn('id', $favoritePlaces->pluck('favoritable_id')->toArray())
          ->get();

        return $places->count() > 0
          ? $places->random(min(10, $places->count()))
          : collect();
      });

      if ($recommendations->isEmpty()) {
        return $this->randomPlacesFallback();
      }

      return [
        'recommendedData' => PlaceResource::collection($recommendations),
        'message' => '',
      ];
    }

    return $this->randomPlacesFallback();
  }

  private function randomPlacesFallback(): array
  {
    $places = Cache::remember('recommendations.random.10', 3600, function () {
      $allPlaces = Place::all();
      $count = min(10, $allPlaces->count());

      return $count > 0 ? $allPlaces->random($count) : collect();
    });

    return [
      'recommendedData' => PlaceResource::collection($places),
      'message' => '',
    ];
  }

  public function execute(User $user)
  {
    $result = $this->resolve($user);

    return ApiTrait::data(
      ['recommendedData' => $result['recommendedData']],
      $result['message'],
      200
    );
  }
}
