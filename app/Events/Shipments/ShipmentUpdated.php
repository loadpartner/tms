<?php

namespace App\Events\Shipments;

use App\Events\Core\TmsEvent;
use App\Models\Shipments\Shipment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class ShipmentUpdated extends TmsEvent implements ShouldBroadcast
{
    public function __construct(
        public readonly Shipment $shipment,
        public readonly array $changedAttributes,
        public readonly array $previousAttributes,
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
        return 'shipment.updated';
    }

    public function getEventData(): array
    {
        return [
            'entity_type' => Shipment::class,
            'entity_id' => $this->shipment->id,
            'shipment_id' => $this->shipment->id,
            'shipment_number' => $this->shipment->shipment_number,
            'changed_attributes' => $this->changedAttributes,
            'previous_attributes' => $this->previousAttributes,
            'current_state' => $this->shipment->state?->getValue(),
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}
