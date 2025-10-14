<?php

namespace App\Observers;

use App\Events\Carriers\CarrierAssigned;
use App\Events\Carriers\CarrierUnassigned;
use App\Events\Shipments\ShipmentCreated;
use App\Events\Shipments\ShipmentDeleted;
use App\Events\Shipments\ShipmentRestored;
use App\Events\Shipments\ShipmentUpdated;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use Illuminate\Support\Collection;

class ShipmentObserver
{
    public function created(Shipment $shipment): void
    {
        event(new ShipmentCreated($shipment, [
            'created_via' => 'model_observer',
            'source' => $this->resolveRequestSource(),
        ]));
    }

    public function updated(Shipment $shipment): void
    {
        $changes = $shipment->getChanges();

        if ($changes === []) {
            return;
        }

        $previousValues = [];

        foreach (array_keys($changes) as $attribute) {
            $previousValues[$attribute] = $shipment->getOriginal($attribute);
        }

        $hasCarrierChange = array_key_exists('carrier_id', $changes);

        if ($hasCarrierChange) {
            $this->dispatchCarrierChangeEvents($shipment);
        }

        event(new ShipmentUpdated(
            shipment: $shipment,
            changedAttributes: $changes,
            previousAttributes: $previousValues,
            metadata: [
                'updated_via' => 'model_observer',
                'has_carrier_change' => $hasCarrierChange,
            ],
        ));
    }

    public function deleted(Shipment $shipment): void
    {
        event(new ShipmentDeleted($shipment, [
            'deleted_via' => 'model_observer',
            'soft_delete' => method_exists($shipment, 'trashed') && $shipment->trashed(),
        ]));
    }

    public function restored(Shipment $shipment): void
    {
        event(new ShipmentRestored($shipment, [
            'restored_via' => 'model_observer',
        ]));
    }

    private function dispatchCarrierChangeEvents(Shipment $shipment): void
    {
        $previousCarrierId = $shipment->getOriginal('carrier_id');
        $newCarrierId = $shipment->getAttribute('carrier_id');

        $carrierIds = array_filter([
            $previousCarrierId,
            $newCarrierId,
        ]);

        /** @var Collection<int, Carrier> $carriers */
        $carriers = $carrierIds === []
            ? collect()
            : Carrier::query()->whereIn('id', $carrierIds)->get()->keyBy('id');

        $previousCarrier = $previousCarrierId ? $carriers->get($previousCarrierId) : null;
        $newCarrier = $newCarrierId ? $carriers->get($newCarrierId) : null;

        if ($previousCarrierId && !$newCarrierId && $previousCarrier) {
            event(new CarrierUnassigned($shipment, $previousCarrier, [
                'unassigned_via' => 'shipment_update',
            ]));

            return;
        }

        if ($newCarrier) {
            event(new CarrierAssigned($shipment, $newCarrier, $previousCarrier, [
                'assigned_via' => 'shipment_update',
                'is_reassignment' => !is_null($previousCarrierId),
            ]));
        }
    }

    private function resolveRequestSource(): string
    {
        return request()->route()?->getName() ?? 'unknown';
    }
}
