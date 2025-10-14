<?php

namespace App\Listeners\Events;

use App\Contracts\Events\TmsEventContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Support\Events\TmsEventRegistry;
use Illuminate\Support\Facades\Schema;

class MetricsListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly TmsEventRegistry $registry)
    {
    }

    public function handle(TmsEventContract $event): void
    {
        $this->updateEventMetrics($event);
        $this->updateBusinessMetrics($event);
    }

    protected function updateEventMetrics(TmsEventContract $event): void
    {
        $timestamp = Carbon::make($event->getOccurredAt()) ?? now();

        // Increment event counter
        $eventCountKey = "metrics:events:{$event->getOrganizationId()}:{$event->getEventType()}:count";
        $this->incrementCounter($eventCountKey);

        // Track event velocity (events per hour)
        $velocityKey = "metrics:events:{$event->getOrganizationId()}:{$event->getEventType()}:velocity:" . $timestamp->copy()->format('Y-m-d-H');
        $this->incrementCounter($velocityKey, $timestamp->copy()->addDay());

        // Update daily metrics
        $this->updateDailyMetrics($event);
    }

    protected function updateBusinessMetrics(TmsEventContract $event): void
    {
        $handler = $this->registry->getMetricsHandler($event->getEventType());

        if ($handler && method_exists($this, $handler)) {
            $this->{$handler}($event);
        }
    }

    protected function updateDailyMetrics(TmsEventContract $event): void
    {
        $timestamp = Carbon::make($event->getOccurredAt()) ?? now();
        $metricsKey = "metrics:daily:{$event->getOrganizationId()}:" . $timestamp->format('Y-m-d');

        $metrics = Cache::get($metricsKey, []);
        $metrics['total_events'] = ($metrics['total_events'] ?? 0) + 1;
        $metrics[$event->getEventType()] = ($metrics[$event->getEventType()] ?? 0) + 1;

        Cache::put($metricsKey, $metrics, $timestamp->copy()->addDays(7));
    }

    protected function incrementShipmentMetrics(TmsEventContract $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->getOrganizationId();
        $timestamp = Carbon::make($event->getOccurredAt()) ?? now();

        // Increment shipment counters
        $this->incrementCounter("metrics:shipments:{$organizationId}:total");
        $this->incrementCounter("metrics:shipments:{$organizationId}:" . $timestamp->format('Y-m'));

        // Track by carrier if assigned
        if (!empty($data['carrier_id'])) {
            $this->incrementCounter("metrics:carriers:{$organizationId}:{$data['carrier_id']}:shipments");
        }
    }

    protected function updateShipmentStateMetrics(TmsEventContract $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->getOrganizationId();

        if (isset($data['previous_state'], $data['current_state'])) {
            $transitionKey = "metrics:shipments:{$organizationId}:transitions:{$data['previous_state']}:{$data['current_state']}";
            $this->incrementCounter($transitionKey);

            if ($data['current_state'] === 'delivered') {
                $this->calculateDeliveryMetrics($event);
            }
        }
    }

    protected function updateCarrierMetrics(TmsEventContract $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->getOrganizationId();
        
        $this->incrementCounter("metrics:carriers:{$organizationId}:{$data['carrier_id']}:assignments");
        $this->storeTimestamp("metrics:carriers:{$organizationId}:{$data['carrier_id']}:last_assignment", $event);
    }

    protected function updateFinancialMetrics(TmsEventContract $event): void
    {
        $data = $event->getEventData();
        $organizationId = $event->getOrganizationId();
        $type = str_contains($event->getEventType(), 'payable') ? 'payables' : 'receivables';

        if (isset($data['amount'])) {
            $timestamp = Carbon::make($event->getOccurredAt()) ?? now();
            $monthKey = "metrics:financial:{$organizationId}:{$type}:" . $timestamp->format('Y-m');
            $this->incrementCounter($monthKey . ':count');
            $this->incrementCounter($monthKey . ':total', ttl: null, by: (float) $data['amount']);
        }
    }

    protected function calculateDeliveryMetrics(TmsEventContract $event): void
    {
        if (! Schema::hasTable('shipment_delivery_metrics')) {
            return;
        }

        $timestamp = Carbon::make($event->getOccurredAt()) ?? now();

        // Use insertOrIgnore to avoid conflicts and handle existing records gracefully
        DB::table('shipment_delivery_metrics')->insertOrIgnore([
            'organization_id' => $event->getOrganizationId(),
            'shipment_id' => $event->getEventData()['shipment_id'],
            'delivered_at' => $timestamp,
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }

    protected function incrementCounter(string $key, ?Carbon $ttl = null, float $by = 1): void
    {
        $current = Cache::get($key, 0);
        $newValue = $current + $by;

        if ($ttl) {
            Cache::put($key, $newValue, $ttl);

            return;
        }

        Cache::forever($key, $newValue);
    }

    protected function storeTimestamp(string $key, TmsEventContract $event): void
    {
        $timestamp = Carbon::make($event->getOccurredAt()) ?? now();
        Cache::forever($key, $timestamp);
    }
}
