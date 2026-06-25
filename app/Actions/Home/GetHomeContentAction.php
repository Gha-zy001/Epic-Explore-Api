<?php

namespace App\Actions\Home;

use App\Actions\Recommendation\GetRecommendedPlacesAction;
use App\Models\Place;
use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Cache;

class GetHomeContentAction
{
  use ApiTrait;

  public function __construct(
    private GetRecommendedPlacesAction $recommendations,
  ) {}

  public function execute(User $user)
  {
    
    $recommendations = $this->recommendations->resolve($user);

    // ddd($recommendations['recommendedData']);
    $places = Cache::remember('home.places', 3600, function () {
      return Place::limit(10)->get();
    });

    $recentActivity = Cache::remember("home.recent_activity.{$user->id}", 3600, function () use ($user) {
      return $user->rewardLogs()
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();
    });

    return ApiTrait::data([
      'user_summary' => [
        'name' => $user->name,
        'level' => $user->level,
        'exp' => $user->exp,
        'avatar' => $user->image,
      ],
      'recommendations' => $recommendations['recommendedData'],
      'places' => $places,
      'recent_activity' => $recentActivity,
      'streak' => 0,
      'daily_quests' => [],
    ], '', 200);
  }
}
