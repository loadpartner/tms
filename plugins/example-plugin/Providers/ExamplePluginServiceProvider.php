<?php

namespace Plugins\ExamplePlugin\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Plugins\ExamplePlugin\Services\ExamplePluginService;

class ExamplePluginServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ExamplePluginService::class, function ($app) {
            return new ExamplePluginService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 