<?php

namespace App\Http\Controllers\Api\v1\User\Favorite;

use App\Actions\Favorite\AddFavoriteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AddFavoriteController extends Controller
{
  public function __invoke(Request $request, string $favoritableType, int $favoritableId, AddFavoriteAction $action)
  {
    return $action->execute($request->user(), $favoritableType, $favoritableId);
  }
}
