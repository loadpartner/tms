<?php

namespace App\Services\Shipments;

use App\Enums\StopType;
use App\Models\Shipments\Shipment;
use App\States\Shipments\AtDelivery;
use App\States\Shipments\AtPickup;
use App\States\Shipments\Delivered;
use App\States\Shipments\Dispatched;
use App\States\Shipments\InTransit;
use App\States\Shipments\ShipmentState;

class ShipmentStateService
{
    /**
     * Calculates the state of the shipment based on the current state of the stops
     * This assumes that the shipment is in a state where stops are still active
     */
    public function calculateStopState(Shipment $shipment): string
    {
        // Check if there are any stops at all
        if (!$shipment->stops()->exists()) {
            return Dispatched::class; // Default if no stops - changed from Booked to Dispatched
        }

        // Get the last stop safely
        $lastStop = $shipment->stops()->latest('stop_number')->first();
        if (!$lastStop) {
            return Dispatched::class; // Safety check
        }

        if ($lastStop->loaded_unloaded_at) {
            return Delivered::class;
        }

        $currentStop = $shipment->current_stop;

        if ($currentStop && $currentStop->stop_type === StopType::Pickup) {
            return AtPickup::class;
        } elseif ($currentStop && $currentStop->stop_type === StopType::Delivery) {
            return AtDelivery::class;
        }

        if ($shipment->next_stop?->id != $shipment->stops()->first()->id) {
            return InTransit::class;
        }

        return Dispatched::class;
    }

    /**
     * Transitions the shipment through the required stop states to achieve the final state
     * based on the correct order
     * This supports multiple stop updates at once while still emitting required events
     */
    public function transitionThroughStopStates(Shipment $shipment, string $finalState): Shipment
    {

        // TODO - There will be a bug in the case of multi picks
        // The pickup state will only be emitted once in the case of all stops being
        // updated at the same time.

        // TODO - This should get pulled in from the ShipmentState class somehow
        // Order of states to be transitioned through
        $stateOrder = [
            Dispatched::class,
            AtPickup::class,
            InTransit::class,
            AtDelivery::class,
            Delivered::class
        ];

        $currentState = $shipment->state::class;

        $currentStateIndex = array_search($currentState, $stateOrder);
        $finalStateIndex = array_search($finalState, $stateOrder);

        // If state is going back to a previous state 
        // then we force transition and skip events by updating 
        // the shipment directly
        // else we transition through the states one by one
        // to reach the final state
        if ($currentStateIndex > $finalStateIndex) {
            $shipment->update([
                'state' => ShipmentState::resolveStateClass($stateOrder[$finalStateIndex])
            ]);
        } elseif ($currentStateIndex < $finalStateIndex) {
            while ($currentStateIndex < $finalStateIndex) {
                $shipment->state->transitionTo($stateOrder[$currentStateIndex + 1]);
                $currentStateIndex++;
            }
        }

        return $shipment;
    }
} 