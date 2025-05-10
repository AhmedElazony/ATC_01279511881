<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Api\Support\Http\Controllers\ApiController;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends ApiController
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    // Request password reset OTP
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $this->otpService->send($user, 'password_reset');
        }

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
