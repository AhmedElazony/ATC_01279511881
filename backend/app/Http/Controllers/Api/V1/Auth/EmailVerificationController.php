<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Api\Support\Http\Controllers\ApiController;
use Api\Support\Http\Traits\VerificationOtpSender;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EmailVerificationController extends ApiController
{
    use VerificationOtpSender;
    public function __construct(protected OtpService $otpService)
    {
    }

    public function sendVerificationOtp(Request $request)
    {
        $this->sendOtp($request, 'email_verification', $this->otpService);

        return $this->success('Verification OTP sent successfully!');
    }

    public function verify(Request $request)
    {
        $validated = request()->validate([
            'email' => ['required', 'email', 'max:255'],
            'otp' => ['required', 'string', 'max:6'],
        ]);

        $result = $this->otpService->verify($validated['email'], $validated['otp']);

        if (!$result['success']) {
            return $this->error($result['message'], $result['statusCode']);
        }

        $user = $result['user'];
        $user->markEmailAsVerified();

        $token = $user->createToken('Api Token')->plainTextToken;

        return $this->success([
            'message' => 'Email verified successfully!',
            'user' => UserResource::make($user),
            'token' => $token
        ]);

    }
}
