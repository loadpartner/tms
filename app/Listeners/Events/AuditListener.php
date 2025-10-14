<?php

namespace App\Listeners\Events;

use App\Contracts\Events\TmsEventContract;
use App\Support\Events\TmsEventRegistry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class AuditListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private readonly TmsEventRegistry $registry)
    {
    }


    public function handle(TmsEventContract $event): void
    {
        try {
            // Only track events that are in our tracked list
            if (!$this->shouldTrackEvent($event)) {
                return;
            }

            // Store event as custom audit entry in OwenIT auditing system
            $audit = $this->createCustomAudit($event);

            // Log the event with full payload for debugging
            $triggeredBy = $event->getTriggeredBy();
            $eventData = $event->getEventData();

            Log::info('Event tracked in audit system', [
                'audit_id' => $audit->id,
                'event_id' => $event->getEventId(),
                'event_type' => $event->getEventType(),
                'organization_id' => $event->getOrganizationId(),
                'triggered_by' => $triggeredBy?->id,
                'event_data' => $eventData, // Full payload for debugging
                'metadata' => $event->getMetadata(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to audit event', [
                'event_id' => $event->getEventId(),
                'event_type' => $event->getEventType(),
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function shouldTrackEvent(TmsEventContract $event): bool
    {
        return in_array($event->getEventType(), $this->registry->auditableEvents(), true);
    }

    protected function createCustomAudit(TmsEventContract $event): Audit
    {
        $eventData = $event->getEventData();
        $entityClass = $eventData['entity_type'] ?? null;
        $entityId = $eventData['entity_id'] ?? null;
        $eventType = $event->getEventType();

        // Prepare old/new values based on event type
        $oldValues = $eventData['previous_attributes'] ?? [];
        $changedAttributes = $eventData['changed_attributes'] ?? [];

        // For unassigned/deleted events, the data should be in old_values, not new_values
        $isUnassignment = str_contains($eventType, 'unassigned') || str_contains($eventType, 'deleted');

        if ($isUnassignment) {
            // Put the entity data in old_values (what was removed)
            $oldValues = array_merge($oldValues, $eventData);
            unset($oldValues['previous_attributes']);
            unset($oldValues['changed_attributes']);

            $newValues = [
                'event_id' => $event->getEventId(),
                'organization_id' => $event->getOrganizationId(),
                'occurred_at' => $event->getOccurredAt()->format('Y-m-d H:i:s'),
                'metadata' => $event->getMetadata(),
            ];
        } else {
            // For assigned/created/updated events, use changed attributes as new values
            // This ensures the audit display logic can properly show old → new transitions
            $newValues = array_merge($changedAttributes, [
                'event_id' => $event->getEventId(),
                'organization_id' => $event->getOrganizationId(),
                'occurred_at' => $event->getOccurredAt()->format('Y-m-d H:i:s'),
                'metadata' => $event->getMetadata(),
            ]);

            // Add additional event data for context (but not the changed/previous attributes)
            foreach ($eventData as $key => $value) {
                if (!in_array($key, ['changed_attributes', 'previous_attributes', 'entity_type', 'entity_id'], true)) {
                    $newValues[$key] = $value;
                }
            }
        }

        // Determine tags based on event type
        $tags = collect([
            'tms-event',
            explode('.', $event->getEventType())[0], // e.g., 'shipment', 'carrier'
        ])->filter()->implode(',');

        $triggeredBy = $event->getTriggeredBy();

        /** @var \Illuminate\Http\Request|null $currentRequest */
        $currentRequest = app()->bound('request') ? request() : null;

        // Use saveQuietly to avoid triggering more events and to handle transactions properly
        $audit = new Audit([
            'auditable_type' => $entityClass,
            'auditable_id' => $entityId,
            'event' => $event->getEventType(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'url' => $currentRequest?->fullUrl(),
            'ip_address' => $currentRequest?->ip(),
            'user_agent' => $currentRequest?->userAgent(),
            'user_type' => $triggeredBy ? $triggeredBy::class : null,
            'user_id' => $triggeredBy?->id,
            'tags' => $tags,
        ]);

        $audit->saveQuietly();

        return $audit;
    }
}
