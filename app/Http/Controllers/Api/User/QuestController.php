<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Quest;
use App\Models\UserQuest;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class QuestController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
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

    public function accept(Request $request, $questId)
    {
        $user = $request->user();
        
        if ($user->quests()->where('quest_id', $questId)->exists()) {
            return ApiTrait::errorMessage([], 'Quest already accepted or completed', 422);
        }

        $user->quests()->attach($questId, ['status' => 'active', 'progress' => 0]);

        return ApiTrait::successMessage('Quest accepted!', 200);
    }
}
