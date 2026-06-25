<?php

namespace App\Http\Controllers\Api\v1\User\Place;

use App\Actions\Place\CheckInPlaceAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CheckInPlaceController extends Controller
{
  public function __invoke(Request $request, CheckInPlaceAction $action)
  {
    $validated = $request->validate([
      'place_id' => 'required|exists:places,id',
      'latitude' => 'nullable|numeric',
      'longitude' => 'nullable|numeric',
    ]);

    return $action->execute(
      $request->user(),
      (int) $validated['place_id'],
      $validated['latitude'] ?? null,
      $validated['longitude'] ?? null,
    );
  }
}
