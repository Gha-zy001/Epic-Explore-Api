<?php

namespace App\Http\Controllers\Api\v1\User\Quest;

use App\Actions\Quest\GetQuestsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListQuestsController extends Controller
{
  public function __invoke(Request $request, GetQuestsAction $action)
  {
    return $action->execute($request->user());
  }
}
