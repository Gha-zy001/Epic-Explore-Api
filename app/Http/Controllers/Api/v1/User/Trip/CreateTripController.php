<?php

namespace App\Http\Controllers\Api\v1\User\Trip;

use App\Actions\Trip\CreateTripAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTripRequest;
use Illuminate\Http\Request;

class CreateTripController extends Controller
{
  public function __invoke(StoreTripRequest $request, CreateTripAction $action)
  {
    return $action->execute(array_merge(
      $request->validated() ?? [],
      ['user_id' => $request->user()->id]
    ));
  }
}
