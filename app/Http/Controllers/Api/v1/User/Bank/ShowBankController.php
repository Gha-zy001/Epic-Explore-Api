<?php

namespace App\Http\Controllers\Api\v1\User\Bank;

use App\Actions\Entity\EntityType;
use App\Actions\Entity\ShowEntityAction;
use App\Http\Controllers\Controller;

class ShowBankController extends Controller
{
  public function __invoke(mixed $BankId, ShowEntityAction $action)
  {
    return $action->execute(EntityType::Bank, $BankId);
  }
}
