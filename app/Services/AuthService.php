<?php

namespace App\Services;

use App\Models\User;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;
use App\Actions\UploadImageAction;


class AuthService
{
  use ApiTrait;

  public function __construct(private UploadImageAction $uploadImageAction)
  {
  }

  public function register(array $data)
  {
    $data['image'] = $this->uploadImageAction->execute($data['image'] ?? null, 'users');
    $user = User::create($data);
    $token = $user->createToken('Api Token of ' . $user->name)->plainTextToken;
    return ApiTrait::data([
      'user' => $user,
      'token' => $token,
    ], 'Registration successful', 201);
  }

  public function login(array $data)
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

  public function logout(array $data)
  {
    $data['token']->delete();
    return ApiTrait::successMessage('You have been logged out and your token has been deleted', 200);
  }
}