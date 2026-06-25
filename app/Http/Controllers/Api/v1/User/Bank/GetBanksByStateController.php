<?php

namespace App\Http\Controllers\Api\v1\User\Bank;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\GetEntitiesByStateAction;
use App\Http\Controllers\Controller;

class GetBanksByStateController extends Controller
{
  public function __invoke(string $stateName, GetEntitiesByStateAction $action)
  {
    return $action->execute(EntityType::Bank, $stateName);
  }
}
