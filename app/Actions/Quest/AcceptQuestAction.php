<?php

namespace App\Actions\Quest;

use App\Models\User;
use App\Traits\ApiTrait;

class AcceptQuestAction
{
  use ApiTrait;

  public function execute(User $user, int $questId)
  {
    if ($user->quests()->where('quest_id', $questId)->exists()) {
      return ApiTrait::errorMessage([], 'Quest already accepted or completed', 422);
    }

    $user->quests()->attach($questId, ['status' => 'active', 'progress' => 0]);

    return ApiTrait::successMessage('Quest accepted!', 200);
  }
}
