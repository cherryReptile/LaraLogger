<?php

namespace App\Providers;

use App\Services\Auth\AppAuth;
use App\Services\Auth\OAuth;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(OAuth::class, function (Application $app) {
            return new OAuth();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
