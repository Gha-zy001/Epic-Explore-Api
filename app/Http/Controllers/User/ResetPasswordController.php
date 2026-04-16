<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
  protected OtpService $otpService;

  public function __construct(OtpService $otpService)
  {
    $this->otpService = $otpService;
  }

  public function reset(ResetPasswordRequest $request)
  {
    $otpValidation = $this->otpService->validate($request->email, $request->otp);
    
    if (!$otpValidation->status) {
      return response()->json(['error' => $otpValidation->message], 401);
    }

    $user = User::where('email', $request->email)->first();
    if ($user) {
        $user->update(['password' => Hash::make($request->password)]);
        $user->tokens()->delete();
        return response()->json(['success' => true, 'message' => 'Password reset successful'], 200);
    }
    
    return response()->json(['error' => 'User not found'], 404);
  }

  public function resets(Request $request)
  {
    return 'Please use the reset password form.';
  }
}

