<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;

Route::group(['prefix' => 'auth', 'as', 'auth.'], function () {
    Route::post('login', [AuthController::class, 'login'])
        ->name('login');
    Route::post('register', [AuthController::class, 'register'])
        ->name('register');
    Route::delete('logout', [AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('logout');
});
