<?php

namespace App\Http\Controllers\Api\v1\User\Home;

use App\Actions\Home\GetHomeContentAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
  public function __invoke(Request $request, GetHomeContentAction $action)
  {
    return $action->execute($request->user());
  }
}
