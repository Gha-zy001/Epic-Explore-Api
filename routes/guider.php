<?php

use App\Http\Controllers\Api\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Guider\GuiderAuthController;




Route::prefix('guider')->group(function () {
  Route::post('/register', [GuiderAuthController::class, 'register'])->middleware('throttle:5,1');
  Route::post('/login', [GuiderAuthController::class, 'login'])->middleware('throttle:5,1');
  Route::post('/verify-otp', [GuiderAuthController::class, 'verifyOtp'])->middleware('throttle:5,1');

  Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [GuiderAuthController::class, 'logout']);
    Route::get('/get_contact', [ContactController::class, 'getContactRequestsForGuider']);
  });
});
