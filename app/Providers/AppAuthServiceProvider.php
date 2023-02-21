<?php

namespace App\Providers;

use App\Services\Auth\AppAuth;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppAuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton(AppAuth::class, function (Application $app) {
            return new AppAuth();
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
