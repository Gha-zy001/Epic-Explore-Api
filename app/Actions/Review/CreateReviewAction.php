<?php

namespace App\Actions\Review;

use App\Models\Hotel;
use App\Models\Place;
use App\Models\Review;
use App\Models\User;
use App\Services\PointService;
use App\Traits\ApiTrait;

class CreateReviewAction
{
  use ApiTrait;

  public function __construct(
    private PointService $pointService,
  ) {}

  public function execute(User $user, string $reviewableType, int $reviewableId, int $starRating, string $comments)
  {
    try {
      $reviewable = match ($reviewableType) {
        'place' => Place::findOrFail($reviewableId),
        'hotel' => Hotel::findOrFail($reviewableId),
        default => null,
      };

      if ($reviewable === null) {
        return ApiTrait::errorMessage([], 'Invalid reviewable type', 422);
      }

      $review = Review::create([
        'user_id' => $user->id,
        'star_rating' => $starRating,
        'comments' => $comments,
        'reviewable_id' => $reviewableId,
        'reviewable_type' => $reviewable::class,
      ]);

      $this->pointService->awardExperience(
        $user,
        25,
        "Reviewed {$reviewable->name}",
        'xp',
        'reviews',
        $review
      );

      return ApiTrait::successMessage('Your review has been submitted Successfully', 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'Fail', 422);
    }
  }
}
