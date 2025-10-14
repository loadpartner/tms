<?php

namespace App\Contracts\Events;

use App\Models\User;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;

interface TmsEventContract extends Arrayable
{
    public function getEventId(): string;

    public function getEventType(): string;

    public function getOrganizationId(): string|int;

    public function getOccurredAt(): DateTimeInterface;

    public function getTriggeredBy(): ?User;

    public function getMetadata(): array;

    public function getEventData(): array;

    public function shouldBroadcast(): bool;
}
