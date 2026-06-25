<?php

namespace App\Actions\Ranking;

use App\Models\User;
use App\Traits\ApiTrait;

class GetLeaderboardAction
{
  use ApiTrait;

  public function execute()
  {
    $topUsers = User::orderBy('exp', 'desc')
      ->limit(10)
      ->get(['name', 'image', 'exp', 'level']);

    return ApiTrait::data(compact('topUsers'));
  }
}
