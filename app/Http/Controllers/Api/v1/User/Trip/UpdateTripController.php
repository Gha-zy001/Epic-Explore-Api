<?php

namespace App\Http\Controllers\Api\v1\User\Trip;

use App\Actions\Trip\UpdateTripAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateTripController extends Controller
{
  public function __invoke(Request $request, mixed $id, UpdateTripAction $action)
  {
    return $action->execute(
      $request->user()->id,
      (int) $id,
      $request->validated() ?? $request->all()
    );
  }
}
