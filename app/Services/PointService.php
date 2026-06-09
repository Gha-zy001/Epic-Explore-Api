<?php

namespace App\Services;

use App\Models\User;
use App\Models\RewardLog;
use App\Models\Quest;
use App\Models\UserQuest;
use Illuminate\Support\Facades\DB;

class PointService
{
    /**
     * Award XP to a user and check quest progress.
     *
     * @param User $user
     * @param int $points
     * @param string $description
     * @param string $type
     * @param string $actionType (visits, reviews, favorites, trips, checkin)
     * @param object|null $reference
     * @return void
     */
    public function awardExperience(User $user, int $points, string $description, string $type = 'xp', string $actionType = 'general', $reference = null)
    {
        DB::transaction(function () use ($user, $points, $description, $type, $actionType, $reference) {
            // Log the reward
            RewardLog::create([
                'user_id' => $user->id,
                'reward_type' => $type,
                'description' => $description,
                'points' => $points,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
            ]);

            // Update user total exp
            $user->exp += $points;

            // Simple level calculation (e.g., Level = sqrt(exp / 100))
            $newLevel = floor(sqrt($user->exp / 100)) + 1;
            
            if ($newLevel > $user->level) {
                $user->level = $newLevel;
                // You could trigger a LevelUp event here
            }

            $user->save();

            // Update quest progress for matching active quests
            $this->updateQuestProgress($user, $actionType, 1);
        });
    }

    /**
     * Update quest progress for user based on action type.
     */
    protected function updateQuestProgress(User $user, string $actionType, int $increment = 1): void
    {
        $activeQuests = $user->quests()
            ->wherePivot('status', 'active')
            ->where('requirement_type', $actionType)
            ->get();

        foreach ($activeQuests as $quest) {
            $pivot = $quest->pivot;
            $newProgress = $pivot->progress + $increment;
            
            $pivot->progress = $newProgress;
            
            if ($newProgress >= $quest->requirement_count) {
                $pivot->status = 'completed';
                // Award quest completion XP
                $user->exp += $quest->reward_xp;
                $user->save();
                
                RewardLog::create([
                    'user_id' => $user->id,
                    'reward_type' => 'quest_completion',
                    'description' => "Completed quest: {$quest->title}",
                    'points' => $quest->reward_xp,
                    'reference_type' => Quest::class,
                    'reference_id' => $quest->id,
                ]);
            }
            
            $pivot->save();
        }
    }

    /**
     * Manually award XP without quest tracking (for admin actions, etc.)
     */
    public function awardRawExperience(User $user, int $points, string $description, string $type = 'xp', $reference = null): void
    {
        DB::transaction(function () use ($user, $points, $description, $type, $reference) {
            RewardLog::create([
                'user_id' => $user->id,
                'reward_type' => $type,
                'description' => $description,
                'points' => $points,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
            ]);

            $user->exp += $points;
            $newLevel = floor(sqrt($user->exp / 100)) + 1;
            
            if ($newLevel > $user->level) {
                $user->level = $newLevel;
            }

            $user->save();
        });
    }
}