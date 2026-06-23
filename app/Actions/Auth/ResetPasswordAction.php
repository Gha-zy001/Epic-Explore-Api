<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Models\Otp;
use App\Traits\ApiTrait;
use Illuminate\Support\Facades\Hash;
use App\Services\OtpService;

class ResetPasswordAction
{
  use ApiTrait;

  public function __construct(private OtpService $otpService)
  {
  }

  public function execute(array $data)
  {
    $otpValidation = $this->otpService->validate($data['email'], $data['otp']);

    if (!$otpValidation->status) {
      return ApiTrait::errorMessage([], $otpValidation->message, 401);
    }

    $user = User::where('email', $data['email'])->first();
    if (!$user) {
      return ApiTrait::errorMessage([], 'User not found', 404);
    }

    $user->update(['password' => Hash::make($data['password'])]);
    $user->tokens()->delete();
    Otp::where('identifier', $data['email'])->update(['valid' => false]);

    return ApiTrait::successMessage('Password reset successful', 200);
  }
}
