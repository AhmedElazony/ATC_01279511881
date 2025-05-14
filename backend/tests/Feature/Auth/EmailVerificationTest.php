<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->otpService = $this->app->make(OtpService::class);
    }

    public function test_user_can_request_verification_otp()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/v1/auth/send-verification-otp', [
            'email' => $user->email,
            'type' => 'email_verification',
        ]);

        $response->assertStatus(200);
    }

    public function test_user_can_verify_email_with_valid_otp()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Manually set an OTP in cache
        $otp = '123456';
        Cache::put('otp_for_' . $user->email, $otp, now()->addMinutes(5));

        $response = $this->postJson('/api/v1/auth/verify-email', [
            'email' => $user->email,
            'otp' => $otp,
        ], [
            'Authorization' => 'Bearer ' . $user->createToken('TestToken')->plainTextToken,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'registered_at'
                    ]
                ]
            ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_user_cannot_verify_email_with_invalid_otp()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        // Manually set an OTP in cache
        $otp = '123456';
        Cache::put('otp_for_' . $user->email, $otp, now()->addMinutes(5));

        $response = $this->postJson('/api/v1/auth/verify-email', [
            'email' => $user->email,
            'otp' => 'invalid-otp',
        ]);

        $response->assertStatus(401);
        $this->assertNull($user->fresh()->email_verified_at);
    }
}
