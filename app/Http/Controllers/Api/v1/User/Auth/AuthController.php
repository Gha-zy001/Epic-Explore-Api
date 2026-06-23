<?php

namespace App\Http\Controllers\Api\v1\User\Auth;

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
use App\Services\AuthService;

class AuthController extends Controller
{

  public function __construct(private AuthService $authService)
  {
  }
  public function login(LoginUserRequest $request)
  {
    return $this->authService->login($request->validated());
  }

  public function register(UserData $request)
  {
    return $this->authService->register($request->validated());
  }

  public function logout(Request $request)
  {
    return $this->authService->logout($request->user());
  }
}
