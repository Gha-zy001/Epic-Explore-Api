<?php

namespace App\Actions\Review;

use App\Models\Hotel;
use App\Models\Place;
use App\Traits\ApiTrait;

class GetReviewsAction
{
  use ApiTrait;

  public function execute(string $reviewableType, int $reviewableId)
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

      $reviews = $reviewable->reviews()->get();
      $rateKey = $reviewableType === 'place' ? 'place rate' : 'hotel rate';

      $avg_rating = [$rateKey => $reviews->avg('star_rating')];

      $userReviews = $reviews->groupBy('user_id')->map(function ($userReviews) {
        $userName = $userReviews->first()->user->name;
        $userReview = $userReviews->map(fn ($review) => [
          'star_rating' => $review->star_rating,
          'comments' => $review->comments,
        ]);

        return [
          'user_name' => $userName,
          'user_reviews' => $userReview,
        ];
      })->values();

      return ApiTrait::data(compact('avg_rating', 'userReviews'), 200);
    } catch (\Throwable) {
      return ApiTrait::errorMessage([], 'There is no Reviews Yet', 404);
    }
  }
}
