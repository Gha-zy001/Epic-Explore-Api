<?php

namespace App\Http\Controllers\Api\v1\User\Trip;

use App\Actions\Trip\DeleteTripAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteTripController extends Controller
{
  public function __invoke(Request $request, mixed $id, DeleteTripAction $action)
  {
    return $action->execute($request->user()->id, (int) $id);
  }
}
