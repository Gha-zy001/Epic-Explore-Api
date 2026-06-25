<?php

namespace App\Http\Controllers\Api\v1\User\Review;

use App\Actions\Review\GetReviewsAction;
use App\Http\Controllers\Controller;

class GetHotelReviewsController extends Controller
{
  public function __invoke(int $hotel_id, GetReviewsAction $action)
  {
    return $action->execute('hotel', $hotel_id);
  }
}
