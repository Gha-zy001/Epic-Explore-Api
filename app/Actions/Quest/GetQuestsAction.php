<?php

namespace App\Actions\Quest;

use App\Models\Quest;
use App\Models\User;
use App\Traits\ApiTrait;

class GetQuestsAction
{
  use ApiTrait;

  public function execute(User $user)
  {
    $activeQuests = $user->quests()
      ->wherePivot('status', 'active')
      ->get();

    $availableQuests = Quest::whereNotIn('id', $user->quests()->pluck('quest_id'))
      ->limit(5)
      ->get();

    return ApiTrait::data([
      'active_quests' => $activeQuests,
      'available_quests' => $availableQuests,
    ]);
  }
}
