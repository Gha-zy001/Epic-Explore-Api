<?php

namespace App\Http\Controllers\Api\v1\User\Favorite;

use App\Actions\Favorite\GetFavoritePlacesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetFavoritePlacesController extends Controller
{
  public function __invoke(Request $request, GetFavoritePlacesAction $action)
  {
    return $action->execute($request->user());
  }
}
