<?php

namespace App\Http\Controllers\Api\v1\User\Review;

use App\Actions\Review\CreateReviewAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewRequest;

class CreateReviewController extends Controller
{
  public function __invoke(
    ReviewRequest $request,
    string $reviewable_type,
    int $reviewable_id,
    CreateReviewAction $action
  ) {
    $validated = $request->validated();

    return $action->execute(
      $request->user(),
      $reviewable_type,
      $reviewable_id,
      (int) $validated['star_rating'],
      $validated['comments'],
    );
  }
}
