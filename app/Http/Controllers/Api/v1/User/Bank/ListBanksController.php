<?php

namespace App\Http\Controllers\Api\v1\User\Bank;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\ListEntitiesAction;
use App\Http\Controllers\Controller;

class ListBanksController extends Controller
{
  public function __invoke(ListEntitiesAction $action)
  {
    return $action->execute(EntityType::Bank);
  }
}
