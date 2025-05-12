<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'v1.'], function () {
    require __DIR__ . '/v1/auth.routes.php';
    require __DIR__ . '/v1/events.routes.php';
});
