<?php

namespace App\Http\Controllers\Api\Guider;

use App\Http\Controllers\Controller;
use App\Models\Guider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterGuiderRequest;
use App\Http\Requests\LoginGuiderRequest;
use App\Services\OtpService;

class GuiderAuthController extends Controller
{

  protected $otpService;

  public function __construct(OtpService $otpService)
  {
    $this->otpService = $otpService;
  }

  public function register(RegisterGuiderRequest $request)
  {
    $request->validated();

    $guider = Guider::create([
      'name' => $request->name,
      'email' => $request->email,
      'phone_number' => $request->phone_number,
      'national_id' => $request->national_id,
      'password' => Hash::make($request->password),
      'description' => $request->description,
    ]);

    try {
      $this->otpService->send($guider->email);
    } catch (\Exception $e) {
      $guider->is_verified = false;
      // You might want to log this error
    }

    return response()->json([
      'message' => 'Registration successful, please verify your email',
      'email' => $guider->email,
    ], 201);
  }

  public function verifyOtp(Request $request)
  {
    $request->validate([
      'email' => 'required|email',
      'verification_code' => 'required|numeric',
    ]);

    $otpValidation = $this->otpService->validate($request->email, $request->verification_code);

    if (!$otpValidation->status) {
      return response()->json(['message' => 'Invalid or expired OTP'], 401);
    }

    $guider = Guider::where('email', $request->email)->first();

    if (!$guider) {
      return response()->json(['message' => 'Guider not found'], 404);
    }

    $guider->is_verified = true;
    $guider->save();

    return response()->json(['message' => 'Email verified successfully'], 200);
  }


  public function login(LoginGuiderRequest $request)
  {
    $request->validated();

    $guider = Guider::where('email', $request->email)->first();

    if (!$guider || !Hash::check($request->password, $guider->password)) {
      return response()->json(['message' => 'Credentials do not match'], 401);
    }

    $token = $guider->createToken('Guider Token')->plainTextToken;

    return response()->json([
      'user' => $guider,
      'token' => $token,
      'message' => 'Login successful',
      'is_verified' => $guider->is_verified,
    ], 200);
  }

  public function logout(Request $request)
  {
    $request->user()->tokens()->delete();

    return response()->json(['message' => 'You have successfully logged out and your token has been deleted'], 200);
  }
}
