<?php

namespace App\Http\Controllers\Api\v1\User\Ranking;

use App\Actions\Ranking\GetLeaderboardAction;
use App\Http\Controllers\Controller;

class LeaderboardController extends Controller
{
  public function __invoke(GetLeaderboardAction $action)
  {
    return $action->execute();
  }
}
