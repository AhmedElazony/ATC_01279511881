<?php

use App\Http\Controllers\Api\V1\BookingController;
use Illuminate\Support\Facades\Route;

Route::group(['as' => 'booking.'], function () {
    Route::post('/events/{event}/book', [BookingController::class, 'store'])
        ->name('store');

    Route::get('/events/booked', [BookingController::class, 'index'])
        ->name('index');
});
