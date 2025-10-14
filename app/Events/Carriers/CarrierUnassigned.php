<?php

namespace App\Events\Carriers;

use App\Events\Core\TmsEvent;
use App\Models\Carriers\Carrier;
use App\Models\Shipments\Shipment;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class CarrierUnassigned extends TmsEvent implements ShouldBroadcast
{
    public function __construct(
        public readonly Shipment $shipment,
        public readonly Carrier $carrier,
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
        return 'carrier.unassigned';
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
            'unassigned_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}
