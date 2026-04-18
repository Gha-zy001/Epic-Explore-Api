<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\OtpService;
use App\Models\Otp;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OtpServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $otpService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->otpService = new OtpService();
    }

    /** @test */
    public function it_can_generate_an_otp()
    {
        $identifier = 'axxgha.zy01@gmail.com';
        $otp = $this->otpService->generate($identifier);

        $this->assertInstanceOf(Otp::class, $otp);
        $this->assertEquals($identifier, $otp->identifier);
        $this->assertTrue($otp->valid);
        $this->assertDatabaseHas('otps', [
            'identifier' => $identifier,
            'token' => $otp->token,
            'valid' => true,
        ]);
    }

    /** @test */
    public function it_invalidates_previous_otps()
    {
        $identifier = 'axxgha.zy01@gmail.com';
        $otp1 = $this->otpService->generate($identifier);
        $otp2 = $this->otpService->generate($identifier);

        $this->assertFalse($otp1->fresh()->valid);
        $this->assertTrue($otp2->valid);
    }

    /** @test */
    public function it_can_validate_an_otp()
    {
        $identifier = 'axxgha.zy01@gmail.com';
        $otp = $this->otpService->generate($identifier);

        $validation = $this->otpService->validate($identifier, $otp->token);

        $this->assertTrue($validation->status);
        $this->assertEquals('OTP is valid', $validation->message);
    }

    /** @test */
    public function it_fails_validation_for_invalid_otp()
    {
        $identifier = 'axxgha.zy01@gmail.com';
        $this->otpService->generate($identifier);

        $validation = $this->otpService->validate($identifier, 'wrong-token');

        $this->assertFalse($validation->status);
        $this->assertEquals('Invalid or expired OTP', $validation->message);
    }

    /** @test */
    public function it_can_send_an_otp_via_email()
    {
        Notification::fake();

        $identifier = 'axxgha.zy01@gmail.com';
        $otp = $this->otpService->send($identifier);

        Notification::assertSentOnDemand(
            OtpNotification::class,
            function ($notification, $channels, $notifiable) use ($identifier) {
                return $notifiable->routes['mail'] === $identifier;
            }
        );
    }
}
