<?php

namespace App\Events\Carriers;

use App\Events\Core\TmsEvent;
use App\Models\Carriers\Carrier;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class CarrierStatusChanged extends TmsEvent implements ShouldBroadcast
{
    public function __construct(
        public readonly Carrier $carrier,
        public readonly string $previousStatus,
        public readonly string $newStatus,
        array $metadata = []
    ) {
        parent::__construct(
            eventId: Str::uuid()->toString(),
            organizationId: $carrier->organization_id,
            occurredAt: now(),
            triggeredBy: auth()->user(),
            metadata: $metadata
        );
    }

    public function getEventType(): string
    {
        return 'carrier.status_changed';
    }

    public function getEventData(): array
    {
        return [
            'entity_type' => Carrier::class,
            'entity_id' => $this->carrier->id,
            'carrier_id' => $this->carrier->id,
            'carrier_name' => $this->carrier->name,
            'previous_status' => $this->previousStatus,
            'new_status' => $this->newStatus,
            'changed_at' => $this->occurredAt->format('Y-m-d H:i:s'),
        ];
    }

    public function shouldBroadcast(): bool
    {
        return true;
    }
}
