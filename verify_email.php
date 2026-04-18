<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\Guider\GuiderAuthController;
use Illuminate\Http\Request;

$email = 'axxgha.zy01@gmail.com';
$code = '864600';

echo "Verifying OTP $code for $email...\n";

try {
    $controller = app(GuiderAuthController::class);
    $request = new Request([
        'email' => $email,
        'verification_code' => $code
    ]);
    
    $response = $controller->verifyOtp($request);
    echo "Response: " . json_encode($response->getData()) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
