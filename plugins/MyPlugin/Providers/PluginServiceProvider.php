<?php

namespace Plugins\MyPlugin\Providers;

use App\Events\ShipmentCreated;
use App\Listeners\MyPluginShipmentCreatedListener;
use App\Providers\PluginServiceProvider as BasePluginServiceProvider;
use Illuminate\Support\Facades\Event;

class PluginServiceProvider extends BasePluginServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        logger()->info('PluginServiceProvider::boot');
        parent::boot();

        Event::listen(ShipmentCreated::class, MyPluginShipmentCreatedListener::class);
    }

    // ...
} 