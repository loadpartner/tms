<?php

namespace App\Events\Carriers;

use App\Events\Core\TmsEvent;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class CarrierAssigned extends TmsEvent implements ShouldBroadcast
{
    public function __construct(
        public readonly Shipment $shipment,
        public readonly Carrier $carrier,
        public readonly ?Carrier $previousCarrier = null,
        array $metadata = []
    ) {
        parent::__construct(
            eventId: Str::uuid()->toString(),
            organizationId: $shipment->organization_id,
            occurredAt: now(),
            triggeredBy: auth()->user(),
            metadata: $metadata
        );
    }

    public function getEventType(): string
    {
        return 'carrier.assigned';
    }

    public function getEventData(): array
    {
        return [
            'entity_type' => Shipment::class,
            'entity_id' => $this->shipment->id,
            'shipment_id' => $this->shipment->id,
            'shipment_number' => $this->shipment->shipment_number,
            'carrier_id' => $this->carrier->id,
            'carrier_name' => $this->carrier->name,
            'previous_carrier_id' => $this->previousCarrier?->id,
            'previous_carrier_name' => $this->previousCarrier?->name,
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}