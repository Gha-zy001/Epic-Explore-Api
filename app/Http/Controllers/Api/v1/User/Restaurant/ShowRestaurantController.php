<?php

namespace App\Http\Controllers\Api\v1\User\Restaurant;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\ShowEntityAction;
use App\Http\Controllers\Controller;

class ShowRestaurantController extends Controller
{
  public function __invoke(mixed $RestaurantId, ShowEntityAction $action)
  {
    return $action->execute(EntityType::Restaurant, $RestaurantId);
  }
}
