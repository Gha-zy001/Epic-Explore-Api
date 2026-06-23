<?php

namespace App\Http\Controllers\Api\v1\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Actions\Auth\ForgetPasswordAction;

class ForgetPasswordController extends Controller
{
  public function __invoke(ForgotPasswordRequest $request, ForgetPasswordAction $action)
  {
    return $action->execute($request->validated());
  }
}
