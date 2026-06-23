<?php

namespace App\Http\Controllers\Api\v1\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Actions\Auth\LoginUserAction;

class LoginController extends Controller
{
  public function __invoke(LoginUserRequest $request, LoginUserAction $action)
  {
    return $action->execute($request->validated());
  }
}
