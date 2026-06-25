<?php

namespace App\Http\Controllers\Api\v1\User\Trip;

use App\Actions\Trip\ListUserTripsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ListTripsController extends Controller
{
  public function __invoke(Request $request, ListUserTripsAction $action)
  {
    return $action->execute($request->user()->id);
  }
}
