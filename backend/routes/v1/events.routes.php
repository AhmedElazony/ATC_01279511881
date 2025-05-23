<?php

use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EventController;

Route::group(['prefix' => 'events', 'as' => 'events.'], function () {
    Route::get('/', [EventController::class, 'index'])
        ->name('index');
    Route::get('/categories', CategoryController::class)
        ->name('categories');

    Route::get('/{event}/show', [EventController::class, 'show'])
        ->name('show');
});
