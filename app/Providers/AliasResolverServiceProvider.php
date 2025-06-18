<?php

namespace App\Providers;

use App\Services\AliasResolverService;
use Illuminate\Support\ServiceProvider;

class AliasResolverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AliasResolverService::class, function ($app) {
            return new AliasResolverService();
        });

        // Register a convenient alias for the facade
        $this->app->alias(AliasResolverService::class, 'alias.resolver');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
} 