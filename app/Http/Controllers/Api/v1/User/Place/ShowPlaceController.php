<?php

namespace App\Http\Controllers\Api\v1\User\Place;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\ShowEntityAction;
use App\Http\Controllers\Controller;

class ShowPlaceController extends Controller
{
  public function __invoke(mixed $place, ShowEntityAction $action)
  {
    return $action->execute(EntityType::Place, $place);
  }
}
