<?php

namespace App\Http\Controllers\Api\v1\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Actions\Auth\LogoutUserAction;

class LogoutController extends Controller
{
  public function __invoke(Request $request, LogoutUserAction $action)
  {
    return $action->execute($request->user());
  }
}
