<?php

namespace App\Http\Controllers\Api\v1\User\Trip;

use App\Actions\Trip\ShowTripAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShowTripController extends Controller
{
  public function __invoke(Request $request, int $tripId, ShowTripAction $action)
  {
    return $action->execute($request->user()->id, $tripId);
  }
}
