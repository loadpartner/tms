<?php

namespace App\Observers;

use App\Events\Carriers\CarrierCreated;
use App\Events\Carriers\CarrierStatusChanged;
use App\Models\Carriers\Carrier;

class CarrierObserver
{
    public function created(Carrier $carrier): void
    {
        event(new CarrierCreated($carrier, [
            'created_via' => 'model_observer',
            'source' => $this->resolveRequestSource(),
        ]));
    }

    public function updated(Carrier $carrier): void
    {
        $changes = $carrier->getChanges();

        if (! array_key_exists('status', $changes)) {
            return;
        }

        $previousStatus = $carrier->getOriginal('status');
        $newStatus = $carrier->getAttribute('status');

        if ($previousStatus === null || $newStatus === null || $previousStatus === $newStatus) {
            return;
        }

        event(new CarrierStatusChanged($carrier, (string) $previousStatus, (string) $newStatus, [
            'updated_via' => 'model_observer',
        ]));
    }

    private function resolveRequestSource(): string
    {
        return request()->route()?->getName() ?? 'unknown';
    }
}
