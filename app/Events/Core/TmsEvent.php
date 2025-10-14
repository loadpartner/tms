<?php

namespace App\Events\Core;

use App\Contracts\Events\TmsEventContract;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class TmsEvent implements TmsEventContract
{
    use Dispatchable, InteractsWithSockets, SerializesModels, BroadcastsToChannels;

    public string $eventId;

    public int|string $organizationId;

    public \DateTimeInterface $occurredAt;

    public ?User $triggeredBy;

    public array $metadata;

    public function __construct(
        string $eventId,
        int|string $organizationId,
        \DateTimeInterface $occurredAt,
        ?User $triggeredBy = null,
        array $metadata = []
    ) {
        $this->eventId = $eventId;
        $this->organizationId = $organizationId;
        $this->occurredAt = $occurredAt;
        $this->triggeredBy = $triggeredBy;
        $this->metadata = $metadata;
    }

    abstract public function getEventType(): string;
    
    abstract public function getEventData(): array;
    
    abstract public function shouldBroadcast(): bool;

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getOrganizationId(): string|int
    {
        return $this->organizationId;
    }

    public function getOccurredAt(): \DateTimeInterface
    {
        return $this->occurredAt;
    }

    public function getTriggeredBy(): ?User
    {
        return $this->triggeredBy;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_type' => $this->getEventType(),
            'organization_id' => $this->organizationId,
            'occurred_at' => $this->occurredAt->format('c'),
            'triggered_by' => $this->triggeredBy?->id,
            'data' => $this->getEventData(),
            'metadata' => $this->metadata,
        ];
    }
}
