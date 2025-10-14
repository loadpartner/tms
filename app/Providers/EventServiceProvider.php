<?php

namespace App\Providers;

use App\Support\Events\TmsEventRegistry;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->app->singleton(TmsEventRegistry::class, function ($app) {
            return new TmsEventRegistry($app['config']->get('tms_events', []));
        });
    }

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        $registry = $this->app->make(TmsEventRegistry::class);

        foreach ($registry->listeners() as $event => $listeners) {
            foreach ($listeners as $listener) {
                $this->app['events']->listen($event, $listener);
            }
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
