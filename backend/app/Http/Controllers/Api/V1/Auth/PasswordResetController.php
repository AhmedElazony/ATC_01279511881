<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Api\Support\Http\Controllers\ApiController;
use Api\Support\Http\Traits\VerificationOtpSender;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends ApiController
{
    use VerificationOtpSender;

    public function __construct(protected OtpService $otpService)
    {
    }

    // Request password reset OTP
    public function forgotPassword(Request $request)
    {
        $this->sendOtp($request, 'password_reset', $this->otpService);

        return $this->success('if this email is registered, you will receive a password reset OTP shortly');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'otp' => 'required|string|size:6',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->first();

        $result = $this->otpService->verify($request->email, $request->otp);
        if (!$result['success']) {
            return $this->error($result['message'], $result['statusCode']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $user->tokens()->delete(); // Revoke all tokens

        return $this->success('Password has been reset successfully');
    }
}
