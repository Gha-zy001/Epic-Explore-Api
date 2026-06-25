<?php

namespace App\Http\Controllers\Api\v1\User\Review;

use App\Actions\Review\GetReviewsAction;
use App\Http\Controllers\Controller;

class GetPlaceReviewsController extends Controller
{
  public function __invoke(int $place_id, GetReviewsAction $action)
  {
    return $action->execute('place', $place_id);
  }
}
