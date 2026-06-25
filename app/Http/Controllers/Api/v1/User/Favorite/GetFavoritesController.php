<?php

namespace App\Http\Controllers\Api\v1\User\Favorite;

use App\Actions\Favorite\GetFavoritesAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetFavoritesController extends Controller
{
  public function __invoke(Request $request, GetFavoritesAction $action)
  {
    return $action->execute($request->user());
  }
}
