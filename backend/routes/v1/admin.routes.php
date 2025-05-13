<?php

use App\Http\Controllers\Api\V1\Admin\EventController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/events', [EventController::class, 'index'])
        ->name('events.index');

    Route::post('/events/store', [EventController::class, 'store'])
        ->name('events.store');

    Route::put('/events/{event}/update', [EventController::class, 'update'])
        ->name('events.update');

    Route::delete('/events/{event}/destroy', [EventController::class, 'destroy'])
        ->name('events.destroy');
});
