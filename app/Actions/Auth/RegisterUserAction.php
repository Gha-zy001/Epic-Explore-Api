<?php

namespace App\Actions\Auth;

use App\Events\UserRegistered;
use App\Models\User;
use App\Traits\ApiTrait;
use App\Actions\Core\UploadImageAction;

class RegisterUserAction
{
  use ApiTrait;

  public function __construct(private UploadImageAction $uploadImageAction)
  {
  }

  public function execute(array $data)
  {
    $data['image'] = $this->uploadImageAction->execute($data['image'] ?? null, 'users');
    $user = User::create($data);
    $token = $user->createToken('Api Token of ' . $user->name)->plainTextToken;
    event(new UserRegistered($user));

    return ApiTrait::data([
      'user' => $user,
      'token' => $token,
    ], 'Registration successful', 201);
  }
}
