<?php

namespace App\Actions\Auth;

use App\Traits\ApiTrait;

class LogoutUserAction
{
  use ApiTrait;

  public function execute($user)
  {
    if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
      $user->currentAccessToken()->delete();
    }

    return ApiTrait::successMessage('You have been logged out and your token has been deleted', 200);
  }
}
