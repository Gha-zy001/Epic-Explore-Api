<?php

namespace App\Http\Controllers\Api\v1\User\Favorite;

use App\Actions\Favorite\DeleteFavoriteAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeleteFavoriteController extends Controller
{
  public function __invoke(Request $request, int $favoritableId, DeleteFavoriteAction $action)
  {
    return $action->execute($request->user(), $favoritableId);
  }
}
