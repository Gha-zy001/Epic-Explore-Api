<?php

namespace App\Actions\Auth;

use App\Models\User;
use App\Traits\ApiTrait;
use App\Services\OtpService;

class ForgetPasswordAction
{
  use ApiTrait;

  public function __construct(private OtpService $otpService)
  {
  }

  public function execute(array $data)
  {
    $user = User::where('email', $data['email'])->first();
    if (!$user) {
      return ApiTrait::errorMessage([], 'User not found', 404);
    }
    $this->otpService->send($data['email']);
    return ApiTrait::data([], 'OTP sent successfully', 200);
  }
}
