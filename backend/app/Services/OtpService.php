<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public const OTP_EXPIRY_MINUTES = 5;
    public function __construct()
    {
    }

    public function send(User $user, string $type = 'email_verification')
    {
        $otp = $this->generate();
        $this->saveInCache($user, $otp);

        Mail::to($user)->send(new OtpMail(
            $user,
            $otp,
            'Email Verification',
            $type,
            self::OTP_EXPIRY_MINUTES
        ));

        return $otp;
    }

    protected function generate()
    {
        return (string) random_int(100000, 999999);
    }

    protected function saveInCache(User $user, string $otp): void
    {
        cache()->put('otp_for_' . $user->email, $otp, now()->addMinutes(self::OTP_EXPIRY_MINUTES));
    }

    public function verify(string $email, string $otp): array
    {
        $user = User::where('email', $email)->first();

        $storedOtp = cache()->get('otp_for_' . $user?->email);

        if (!$user || !$storedOtp || $storedOtp !== $otp) {
            return [
                'success' => false,
                'user' => null,
                'message' => 'Invalid Email or OTP!',
                'statusCode' => 401,
            ];
        }

        cache()->forget('otp_for_' . $user->email);

        return [
            'success' => true,
            'user' => $user,
            'message' => 'OTP verified successfully',
            'statusCode' => 200
        ];
    }
}
