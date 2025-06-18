<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use App\Services\Shipments\ShipmentStateService;
use App\States\Shipments\Booked;
use App\States\Shipments\Dispatched;
use App\States\Shipments\Pending;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UncancelShipment
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        ?ShipmentStateService $stateService = null
    ): Shipment {
        $stateService = $stateService ?? new ShipmentStateService();
        
        // Simple transition logic:
        // - If no carrier, transition to Pending
        // - If has carrier, transition to Booked → Dispatched
        // - Then use stateService for advanced states based on stop progress
        
        if (!$shipment->carrier_id) {
            $shipment->state->transitionTo(Pending::class);
            return $shipment;
        }
        
        // Transition to Booked when there's a carrier
        $shipment->state->transitionTo(Booked::class);
        
        // Check if there are stops with progress
        $hasStopProgress = $shipment->stops()->whereNotNull('arrived_at')->exists();
        if ($hasStopProgress) {
            // If there's stop progress, we need to first transition to Dispatched
            // since the ShipmentStateService expects to start from there
            $shipment->state->transitionTo(Dispatched::class);
            
            // Calculate the target state based on stop progress
            $targetState = $stateService->calculateStopState($shipment);
            
            // Only continue if the target state is more advanced than Dispatched
            if ($targetState !== Dispatched::class) {
                // Use the service method to transition through states properly
                // This will handle transitions like Dispatched → AtPickup → InTransit etc.
                $stateService->transitionThroughStopStates($shipment, $targetState);
            }
        }
        
        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
        );

        return redirect()->back()->with('success', 'Shipment uncanceled successfully');
    }

    public function rules(): array
    {
        return [
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
