<?php

namespace Plugins\MyPlugin\Listeners;

use App\Events\ShipmentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MyPluginShipmentCreatedListener implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ShipmentCreated $event): void
    {
        // Plugin logic to execute when a shipment is created
        \Log::info("Plugin MyPlugin: Shipment Created Event - Shipment ID: {$event->shipment->id}");
        // ... plugin specific actions
    }
} 