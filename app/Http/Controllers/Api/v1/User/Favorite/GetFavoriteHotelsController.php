<?php

namespace App\Http\Controllers\Api\v1\User\Favorite;

use App\Actions\Favorite\GetFavoriteHotelsAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GetFavoriteHotelsController extends Controller
{
  public function __invoke(Request $request, GetFavoriteHotelsAction $action)
  {
    return $action->execute($request->user());
  }
}
