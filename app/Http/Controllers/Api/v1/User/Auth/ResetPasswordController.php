<?php

namespace App\Http\Controllers\Api\v1\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Actions\Auth\ResetPasswordAction;

class ResetPasswordController extends Controller
{
  public function __invoke(ResetPasswordRequest $request, ResetPasswordAction $action)
  {
    return $action->execute($request->validated());
  }
}
