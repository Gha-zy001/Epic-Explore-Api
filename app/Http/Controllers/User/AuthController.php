<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserData;
use App\Http\Requests\LoginUserRequest;
use App\Traits\ApiTrait;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use Exception;

class AuthController extends Controller
{
  public function login(LoginUserRequest $request)
  {
    $request->validated();

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
      Log::info('Failed login attempt', ['email' => $request->email, 'ip' => $request->ip()]);
      return ApiTrait::errorMessage([], 'Credentials do not match', 401);
    }

    $token = $user->createToken('user')->plainTextToken;

    return ApiTrait::data([
      'id' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'exp' => (int) $user->exp,
      'level' => (int) $user->level,
      'token' => $token,
    ], 'Login successful', 200);
  }

  /**
   * @bodyParam name string required The user's name. Example: John Doe
   * @bodyParam email string required The user's email. Example: john@example.com
   * @bodyParam password string required The user's password. Example: securePassword123
   * @bodyParam password_confirmation string required Password confirmation (must match password). Example: securePassword123
   */
  public function register(UserData $request)
  {
    $request->validated();

    try {
      return DB::transaction(function () use ($request) {
        $imageName = null;
        if ($request->hasFile('image')) {
          $ext = $request->file('image')->getClientOriginalExtension();
          $imageName = Str::random(20) . '_' . time() . '.' . $ext;
          $request->file('image')->move(public_path('storage/images'), $imageName);
        }

        $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => Hash::make($request->password),
          'image' => $imageName,
          'exp' => 0,
          'level' => 1,
        ]);

        $token = $user->createToken('Api Token of ' . $user->name)->plainTextToken;

        return ApiTrait::data([
          'user' => $user,
          'token' => $token,
        ], 'Registration successful', 201);
      });
    } catch (QueryException $e) {
      if (($e->errorInfo[1] ?? 0) === 1062) {
        Log::error('Duplicate email registration attempt', ['email' => $request->email, 'error' => $e->getMessage()]);
        return ApiTrait::errorMessage([], 'This email is already registered', 409);
      }
      Log::error('Registration database error', ['error' => $e->getMessage(), 'email' => $request->email]);
      return ApiTrait::errorMessage([], 'Registration failed. Please try again.', 500);
    } catch (Exception $e) {
      Log::error('Registration error', ['error' => $e->getMessage(), 'email' => $request->email]);
      return ApiTrait::errorMessage([], 'Registration failed. Please try again.', 500);
    }
  }

  public function logout(Request $request)
  {
    try {
      $request->user()->tokens()->delete();
      return ApiTrait::successMessage('You have been logged out and your token has been deleted', 200);
    } catch (Exception $e) {
      Log::error('Logout error', ['error' => $e->getMessage()]);
      return ApiTrait::errorMessage([], 'Logout failed. Please try again.', 500);
    }
  }
}
