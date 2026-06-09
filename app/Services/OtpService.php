<?php

namespace App\Services;

use App\Models\Otp;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OtpService
{
    public const MAX_ATTEMPTS = 5;
    public const DEFAULT_TTL_MINUTES = 15;

    /**
     * Generate and send an OTP for an identifier (e.g., email).
     */
    public function send(string $identifier, string $type = 'numeric', int $length = 6, int $expiry = self::DEFAULT_TTL_MINUTES)
    {
        $otp = $this->generate($identifier, $type, $length, $expiry);

        try {
            Notification::route('mail', $identifier)->notify(new OtpNotification($otp->token));
        } catch (\Throwable $e) {
            Log::error('OTP notification failed', [
                'identifier' => $identifier,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $otp;
    }

    /**
     * Generate an OTP for an identifier (e.g., email).
     */
    public function generate(string $identifier, string $type = 'numeric', int $length = 6, int $expiry = self::DEFAULT_TTL_MINUTES)
    {
        Otp::where('identifier', $identifier)->update(['valid' => false]);

        $token = $this->generateToken($type, $length);

        $otp = Otp::create([
            'identifier' => $identifier,
            'token' => $token,
            'valid' => true,
        ]);

        $this->resetAttempts($identifier);

        return $otp;
    }

    /**
     * Validate an OTP. Throws on too many attempts; returns false on invalid/expired.
     */
    public function validate(string $identifier, string $token)
    {
        if ($this->tooManyAttempts($identifier)) {
            Log::warning('OTP brute-force attempt blocked', ['identifier' => $identifier]);
            return (object) [
                'status' => false,
                'message' => 'Too many attempts. Please request a new OTP.',
            ];
        }

        $this->incrementAttempts($identifier);

        $otp = Otp::where('identifier', $identifier)
            ->where('token', $token)
            ->where('valid', true)
            ->where('created_at', '>=', now()->subMinutes(self::DEFAULT_TTL_MINUTES))
            ->first();

        if (!$otp) {
            return (object) ['status' => false, 'message' => 'Invalid or expired OTP'];
        }

        $this->resetAttempts($identifier);

        return (object) ['status' => true, 'message' => 'OTP is valid'];
    }

    /**
     * Cryptographically secure token generator.
     */
    protected function generateToken(string $type, int $length): string
    {
        if ($type === 'numeric') {
            $max = (10 ** $length) - 1;
            $min = 10 ** ($length - 1);
            return (string) random_int($min, $max);
        }

        return Str::random($length);
    }

    /**
     * Attempt counter helpers (per identifier, per 15-min window).
     */
    protected function attemptsKey(string $identifier): string
    {
        return 'otp_attempts:' . sha1($identifier);
    }

    protected function incrementAttempts(string $identifier): void
    {
        $key = $this->attemptsKey($identifier);
        Cache::add($key, 0, now()->addMinutes(self::DEFAULT_TTL_MINUTES));
        Cache::increment($key);
    }

    protected function resetAttempts(string $identifier): void
    {
        Cache::forget($this->attemptsKey($identifier));
    }

    protected function tooManyAttempts(string $identifier): bool
    {
        return (int) Cache::get($this->attemptsKey($identifier), 0) >= self::MAX_ATTEMPTS;
    }
}
