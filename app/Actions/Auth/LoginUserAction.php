<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Hash;

class LoginUserAction
{
  use ApiTrait;

  public function execute(array $data)
  {
    $user = User::where('email', $data['email'])->first();
    if (!$user || !Hash::check($data['password'], $user->password)) {
      return ApiTrait::errorMessage([], 'Credentials do not match', 401);
    } else {
      $token = $user->createToken('user')->plainTextToken;
    }

    return ApiTrait::data([
      'user' => $user,
      'token' => $token,
    ], 'Login successful', 200);
  }
}
