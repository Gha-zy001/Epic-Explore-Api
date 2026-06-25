<?php

namespace App\Http\Controllers\Api\v1\User\Trip;

use App\Actions\Trip\UploadTripImagesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadTripImagesController extends Controller
{
  public function __invoke(Request $request, int $tripId, UploadTripImagesAction $action)
  {
    $request->validate([
      'images.*' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ]);

    return $action->execute(
      $request->user()->id,
      $tripId,
      $request->file('images', [])
    );
  }
}
