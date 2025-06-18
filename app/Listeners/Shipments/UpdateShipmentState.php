<?php

namespace App\Listeners\Shipments;

use App\Enums\StopType;
use App\Events\Shipments\ShipmentCarrierBounced;
use App\Events\Shipments\ShipmentCarrierUpdated;
use App\Events\Shipments\ShipmentStateChanged;
use App\Events\Shipments\ShipmentStopsUpdated;
use App\Models\Shipments\Shipment;
use App\Services\Shipments\ShipmentStateService;
use App\States\Shipments\AtDelivery;
use App\States\Shipments\AtPickup;
use App\States\Shipments\Booked;
use App\States\Shipments\Canceled;
use App\States\Shipments\Delivered;
use App\States\Shipments\Dispatched;
use App\States\Shipments\InTransit;
use App\States\Shipments\Pending;
use App\States\Shipments\ShipmentState;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateShipmentState
{
    protected ShipmentStateService $stateService;

    /**
     * Create the event listener.
     */
    public function __construct(ShipmentStateService $stateService)
    {
        $this->stateService = $stateService;
    }

    /**
     * Handle the event.
     */
    public function handle(ShipmentCarrierUpdated|ShipmentStopsUpdated|ShipmentCarrierBounced|ShipmentStateChanged $event): void
    {
        switch (get_class($event)) {
            case ShipmentCarrierUpdated::class:
                $this->handleCarrierChanged($event);
                break;
            case ShipmentStopsUpdated::class:
                $this->handleStopsChanged($event);
                break;
            case ShipmentCarrierBounced::class:
                $this->handleCarrierBounced($event);
                break;
            case ShipmentStateChanged::class:
                $this->handleStateChanged($event);
                break;
        }
    }

    protected function handleCarrierChanged(ShipmentCarrierUpdated $event): void
    {
        if ($event->shipment->state::class === Pending::class) {
            $event->shipment->state->transitionTo(Booked::class);
        }
    }

    protected function handleStopsChanged(ShipmentStopsUpdated $event): void
    {

        if (
            !in_array(
                $event->shipment->state::class,
                [
                    Dispatched::class,
                    InTransit::class,
                    AtPickup::class,
                    AtDelivery::class,
                    Delivered::class
                ]
            )
        ) {
            // If shipment is not in a state where stops are still active then
            // we can just return since we dont want to 
            // update the state for a pending or completed shipment
            return;
        }


        $shipment = $event->shipment;

        $finalState = $this->stateService->calculateStopState($shipment);

        $shipment = $this->stateService->transitionThroughStopStates($shipment, $finalState);
    }

    protected function handleCarrierBounced(ShipmentCarrierBounced $event): void
    {
        if (
            in_array(
                $event->shipment->state::class,
                [
                    Booked::class,
                    Dispatched::class,
                    AtPickup::class
                ]
            )
        ) {
            // The shipment has likely not been loaded, so we can move it 
            // back to pending so the user can rebook it
            $event->shipment->state->transitionTo(Pending::class);
        }
    }

    /**
     * Handle state changes, specifically for uncanceling shipments
     */
    protected function handleStateChanged(ShipmentStateChanged $event): void
    {
        // Check if this is an uncancel operation (transitioning FROM Canceled)
        if (get_class($event->initialState) === Canceled::class) {
            $shipment = $event->model;
            
            // Skip if the shipment doesn't have a carrier - it should stay in Pending
            if (!$shipment->carrier_id) {
                return;
            }
            
            // Only recalculate if we've reached Dispatched state
            // This prevents interference with the normal Canceled → Booked → Dispatched flow
            if (get_class($shipment->state) !== Dispatched::class) {
                return;
            }
            
            // If the shipment has a carrier, recalculate the proper state based on stop progress
            $finalState = $this->stateService->calculateStopState($shipment);
            
            // Don't transition if we're already in the correct state
            if ($finalState === get_class($shipment->state)) {
                return;
            }
            
            // Use the existing method to transition through states
            $this->stateService->transitionThroughStopStates($shipment, $finalState);
        }
    }
}
