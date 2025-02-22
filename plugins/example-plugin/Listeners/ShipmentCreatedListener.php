<?php

namespace Plugins\ExamplePlugin\Listeners;

use App\Events\ShipmentCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ShipmentCreatedListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(ShipmentCreated $event): void
    {
        // Example: Log shipment creation event in plugin context
        Log::info('Shipment Created Event Handled by Example Plugin', [
            'shipment_id' => $event->shipment->id,
            'plugin' => 'example-plugin',
        ]);

        // You can add more plugin-specific logic here, like sending notifications, etc.
    }
} 