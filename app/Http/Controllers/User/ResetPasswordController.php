<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Exception;

class ResetPasswordController extends Controller
{
  protected OtpService $otpService;

  public function __construct(OtpService $otpService)
  {
    $this->otpService = $otpService;
  }

  /**
   * @bodyParam email string required The user's email. Example: john@example.com
   * @bodyParam otp string required One-time password sent to email. Example: 123456
   * @bodyParam password string required New password. Example: newSecurePassword123
   * @bodyParam password_confirmation string required Password confirmation (must match password). Example: newSecurePassword123
   */
  public function reset(ResetPasswordRequest $request)
  {
    try {
      return DB::transaction(function () use ($request) {
        $otpValidation = $this->otpService->validate($request->email, $request->otp);
        
        if (!$otpValidation->status) {
          return response()->json(['error' => $otpValidation->message], 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
          return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['password' => Hash::make($request->password)]);
        $user->tokens()->delete();
        
        return response()->json(['success' => true, 'message' => 'Password reset successful'], 200);
      });
    } catch (QueryException $e) {
      Log::error('Password reset database error', ['error' => $e->getMessage(), 'email' => $request->email]);
      return response()->json(['error' => 'Password reset failed. Please try again.'], 500);
    } catch (Exception $e) {
      Log::error('Password reset error', ['error' => $e->getMessage(), 'email' => $request->email]);
      return response()->json(['error' => 'Password reset failed. Please try again.'], 500);
    }
  }

  public function resets(Request $request)
  {
    return 'Please use the reset password form.';
  }
}

