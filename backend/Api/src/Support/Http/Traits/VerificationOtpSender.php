<?php

namespace Api\Support\Http\Traits;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;

trait VerificationOtpSender
{
    /**
     * Send OTP to the user for email verification or password reset.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $type
     * @param \App\Services\OtpService $otpService
     * @return void
     */
    public function sendOtp(Request $request, string $type, OtpService $otpService): void
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'type' => ['required', 'string', 'in:email_verification,password_reset'],
        ]);

        $user = User::where('email', $request->email)->first();
        if ($user) {
            $this->otpService->send($user, $request->type);
        }
    }
}
