<?php

use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Auth\AuthController;

Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login');
    Route::post('register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register');
    Route::delete('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('logout');

    Route::post('verify-email', [EmailVerificationController::class, 'verify'])
        ->middleware(['auth:sanctum', 'throttle:3,1'])
        ->name('verify-email');

    Route::post('send-verification-email', [EmailVerificationController::class, 'sendVerificationEmail'])
        ->middleware(['auth:sanctum', 'throttle:2,1'])
        ->name('send-verification-email');

    Route::post('forgot-password', [PasswordResetController::class, 'forgotPassword'])
        ->middleware('throttle:2,1')
        ->name('forgot-password');

    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])
        ->middleware('throttle:2,1')
        ->name('reset-password');

    Route::get('user', [AuthController::class, 'user'])
        ->middleware('auth:sanctum')
        ->name('user');
});
