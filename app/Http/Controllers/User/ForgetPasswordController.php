<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Notifications\ResetPasswordNotification as ResetPassword;
use App\Models\User;
use App\Services\OtpService;
use App\Traits\ApiTrait;

class ForgetPasswordController extends Controller
{
  protected OtpService $otpService;

  public function __construct(OtpService $otpService)
  {
    $this->otpService = $otpService;
  }

  public function fogotPassword(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
    ]);

    $user = User::where('email', $request->email)->first();
    
    if (!$user) {
        return ApiTrait::errorMessage([], 'User not found', 404);
    }

    $user->notify(new ResetPassword());
    
    $otp = $this->otpService->generate($request->email);
    $token = $otp->token;

    return ApiTrait::data(compact('token'), "OTP sent successfully", 200);
  }
}

