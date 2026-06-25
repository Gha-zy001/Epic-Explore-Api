<?php

namespace App\Actions\Profile;

use App\Models\User;
use App\Traits\ApiTrait;

class ShowProfileAction
{
  use ApiTrait;

  public function execute(User $user)
  {
    $userData = [
      'name' => $user->name,
      'email' => $user->email,
      'avatar' => $user->image,
      'exp' => $user->exp,
      'level' => $user->level,
    ];

    return ApiTrait::data(compact('userData'));
  }
}
