<?php

use App\Http\Controllers\Api\V1\HomeController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
    require __DIR__ . '/v1/auth.routes.php';
    require __DIR__ . '/v1/events.routes.php';

    Route::get('/home', HomeController::class)
        ->name('home');

    Route::group(['middleware' => 'auth:sanctum'], function () {
        require __DIR__ . '/v1/booking.routes.php';
    });

    Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {
        require __DIR__ . '/v1/admin.routes.php';
    });
});
