<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\User\RecommendationController;
use App\Models\Place;
use App\Traits\ApiTrait;

class HomeController extends Controller
{
  public function homeContent(Request $request)
  {
    $user = $request->user();
    
    $recommendationsData = (new RecommendationController())
      ->recommendPlaces();
      
    $places = Place::limit(10)->get();
    
    $recentActivity = $user->rewardLogs()
      ->orderBy('created_at', 'desc')
      ->limit(5)
      ->get();

    $data = [
      'user_summary' => [
          'name' => $user->name,
          'level' => $user->level,
          'exp' => $user->exp,
          'avatar' => $user->image,
      ],
      'recommendations' => $recommendationsData,
      'places' => $places,
      'recent_activity' => $recentActivity,
      'streak' => 0, // Placeholder
      'daily_quests' => [], // Placeholder
    ];
    
    return ApiTrait::data($data, '', 200);
  }
}
