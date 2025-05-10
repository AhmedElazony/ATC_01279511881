<?php

namespace Api\Providers;

use Api\Support\Services\ApiResponse\JsonResponder;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('api-responder', static function () {
            return new JsonResponder();
        });
    }

    public function boot()
    {
    }
}
