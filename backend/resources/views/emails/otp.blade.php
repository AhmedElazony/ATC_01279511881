@component('mail::message')
# {{ $type === 'email_verification' ? 'Verify Your Email Address' : 'Reset Your Password' }}

Hi {{ $user->name }},

Here is your one-time password (OTP) for {{ $type === 'email_verification' ? 'email verification' : 'password reset' }}:

@component('mail::panel')
<div style="text-align: center; font-size: 24px; letter-spacing: 8px; font-weight: bold;">
{{ $otp }}
</div>
@endcomponent

This code will expire in {{ $expiryMinutes }} minutes.

@if ($type === 'email_verification')
Please enter this code to verify your email address.
@else
Please use this code to reset your password.
@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
