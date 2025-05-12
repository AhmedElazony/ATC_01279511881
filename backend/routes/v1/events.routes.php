<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EventController;

Route::group(['prefix' => 'events', 'as' => 'events.'], function () {
    Route::get('/', [EventController::class, 'index'])
        ->name('index');

    Route::get('/{event}', [EventController::class, 'show'])
        ->name('show');

});
