<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserData;
use App\Http\Requests\LoginUserRequest;
use App\Traits\ApiTrait;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
  public function login(LoginUserRequest $request)
  {
    $request->validated();
    $user = User::where('email', $request->email)->first();
    if (!$user || !Hash::check($request->password, $user->password)) {
      return ApiTrait::errorMessage([], 'Credentials do not match', 401);
    }
    $token = $user->createToken('user')->plainTextToken;
    $user->token = $token;
    return response()->json([
      'id' => $user['id'],
      'name' => $user['name'],
      'email' => $user['email'],
      'token' => $token,
      'message' => 'Login successful',
    ], 200);
  }

  public function register(UserData $request)
  {
    $request->validated();

    if ($request->hasFile('image')) {
      $imageName = time() . '.' . $request->file('image')->getClientOriginalExtension();
      $request->file('image')->move(public_path('storage/images'), $imageName);
    } else {
      $imageName = 'null';
    }


    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
      'image' => $imageName,
    ]);

    return ApiTrait::data([
      'user' => $user,
      'token' => $user
        ->createToken('Api Token of' . $user->name)
        ->plainTextToken
    ], "Done", 200);
  }

  public function logout(Request $request)
  {
    $request->user()->tokens()->delete();

    return ApiTrait::successMessage(
      "You have successfully have been loged out and your token has been deleted",
      200
    );
  }
}
