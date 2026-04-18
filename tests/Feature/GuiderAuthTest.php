<?php

namespace Tests\Feature;

use App\Models\Guider;
use App\Notifications\OtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GuiderAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_guider_can_register_and_receives_an_otp_via_email()
    {
        Notification::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'axxgha.zy01@gmail.com',
            'phone_number' => '1234567890',
            'national_id' => '12345678901234',
            'password' => 'password123',
            'description' => 'A professional guider',
        ];

        $response = $this->postJson('/api/guider/register', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Registration successful, please verify your email',
                'email' => 'axxgha.zy01@gmail.com',
            ]);

        $this->assertDatabaseHas('guiders', [
            'email' => 'axxgha.zy01@gmail.com',
            'is_verified' => false,
        ]);

        Notification::assertSentOnDemand(OtpNotification::class);
    }

    /** @test */
    public function a_guider_can_verify_their_email_with_an_otp()
    {
        $guider = Guider::create([
            'name' => 'Jane Doe',
            'email' => 'axxgha.zy01@gmail.com',
            'phone_number' => '0987654321',
            'national_id' => '43210987654321',
            'password' => 'password123',
            'description' => 'Another professional guider',
            'is_verified' => false,
        ]);

        // Generate OTP manually for the guider
        $otpService = app(\App\Services\OtpService::class);
        $otp = $otpService->generate($guider->email);

        $response = $this->postJson('/api/guider/verify-otp', [
            'email' => $guider->email,
            'verification_code' => $otp->token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email verified successfully']);

        $this->assertTrue($guider->fresh()->is_verified);
    }
}
