<?php

namespace App\Listeners\Shipments;

use App\Actions\Documents\Generators\GenerateCustomerInvoice;
use App\Events\Shipments\ShipmentStateChanged;
use App\States\Shipments\Delivered;
use Illuminate\Support\Facades\Log;

class GenerateInvoicesOnDelivery
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ShipmentStateChanged $event): void
    {
        // Check if the shipment has transitioned to the Delivered state
        if ($event->finalState::class !== Delivered::class) {
            return;
        }

        // Get the shipment from the event
        $shipment = $event->model;

        // Generate invoices for each customer on the shipment
        foreach ($shipment->customers as $customer) {
            try {
                GenerateCustomerInvoice::run($shipment, $customer);
                
                Log::info('Generated customer invoice', [
                    'shipment_id' => $shipment->id,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'shipment_number' => $shipment->shipment_number,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to generate customer invoice', [
                    'shipment_id' => $shipment->id,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'shipment_number' => $shipment->shipment_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
} 