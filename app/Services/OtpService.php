<?php

namespace App\Services;

use App\Models\Otp;

use Illuminate\Support\Str;

class OtpService
{
    /**
     * Generate an OTP for an identifier (e.g., email).
     */
    public function generate(string $identifier, string $type = 'numeric', int $length = 6, int $expiry = 15)
    {
        // Mark previous OTPs as invalid
        Otp::where('identifier', $identifier)->update(['valid' => false]);

        $token = $this->generateToken($type, $length);

        return Otp::create([
            'identifier' => $identifier,
            'token' => $token,
            'valid' => true,
        ]);
    }

    /**
     * Validate an OTP.
     */
    public function validate(string $identifier, string $token)
    {
        $otp = Otp::where('identifier', $identifier)
            ->where('token', $token)
            ->where('valid', true)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->first();

        if (!$otp) {
            return (object) ['status' => false, 'message' => 'Invalid or expired OTP'];
        }

        return (object) ['status' => true, 'message' => 'OTP is valid'];
    }

    /**
     * Internal token generator.
     */
    protected function generateToken(string $type, int $length)
    {
        if ($type === 'numeric') {
            return str_pad(rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        }
        
        return Str::random($length);
    }
}
