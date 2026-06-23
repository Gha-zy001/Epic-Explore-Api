<?php

namespace App\Http\Controllers\Api\v1\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserData;
use App\Actions\Auth\RegisterUserAction;

class RegisterController extends Controller
{
  public function __invoke(UserData $request, RegisterUserAction $action)
  {
    return $action->execute($request->validated());
  }
}
