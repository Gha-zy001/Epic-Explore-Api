<?php

namespace App\Http\Controllers\Api\v1\User\Restaurant;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\GetEntitiesByStateAction;
use App\Http\Controllers\Controller;

class GetRestaurantsByStateController extends Controller
{
  public function __invoke(string $stateName, GetEntitiesByStateAction $action)
  {
    return $action->execute(EntityType::Restaurant, $stateName);
  }
}
