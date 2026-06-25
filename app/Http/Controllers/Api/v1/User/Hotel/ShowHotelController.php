<?php

namespace App\Http\Controllers\Api\v1\User\Hotel;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\ShowEntityAction;
use App\Http\Controllers\Controller;

class ShowHotelController extends Controller
{
  public function __invoke(mixed $hotelId, ShowEntityAction $action)
  {
    return $action->execute(EntityType::Hotel, $hotelId);
  }
}
