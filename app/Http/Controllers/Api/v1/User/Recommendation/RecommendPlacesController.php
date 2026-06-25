<?php

namespace App\Http\Controllers\Api\v1\User\Recommendation;

use App\Actions\Recommendation\GetRecommendedPlacesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RecommendPlacesController extends Controller
{
  public function __invoke(Request $request, GetRecommendedPlacesAction $action)
  {
    return $action->execute($request->user());
  }
}
