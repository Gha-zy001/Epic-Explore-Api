<?php

namespace App\Services;

use App\Models\User;
use App\Models\RewardLog;
use Illuminate\Support\Facades\DB;

class PointService
{
    /**
     * Award XP to a user.
     *
     * @param User $user
     * @param int $points
     * @param string $description
     * @param string $type
     * @param object|null $reference
     * @return void
     */
    public function awardExperience(User $user, int $points, string $description, string $type = 'xp', $reference = null)
    {
        DB::transaction(function () use ($user, $points, $description, $type, $reference) {
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
        });
    }
}
