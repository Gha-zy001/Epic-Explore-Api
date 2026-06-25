<?php

namespace App\Http\Controllers\Api\v1\User\Hotel;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\ListEntitiesAction;
use App\Http\Controllers\Controller;

class ListHotelsController extends Controller
{
  public function __invoke(ListEntitiesAction $action)
  {
    return $action->execute(EntityType::Hotel);
  }
}
