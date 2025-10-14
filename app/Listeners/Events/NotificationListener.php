<?php

namespace App\Listeners\Events;

use App\Contracts\Events\TmsEventContract;
use App\Models\User;
use App\Notifications\EventNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotificationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(TmsEventContract $event): void
    {
        if (!$this->shouldNotify($event)) {
            return;
        }

        $users = $this->getNotificationRecipients($event);
        
        if ($users->isEmpty()) {
            return;
        }

        Notification::send($users, new EventNotification($event));
    }

    protected function shouldNotify(TmsEventContract $event): bool
    {
        // Check if the event type is configured for notifications
        $notifiableEvents = config('events.notifiable', [
            'shipment.created',
            'shipment.state_changed',
            'carrier.assigned',
            'shipment.carrier_bounced',
        ]);

        return in_array($event->getEventType(), $notifiableEvents);
    }

    protected function getNotificationRecipients(TmsEventContract $event): \Illuminate\Database\Eloquent\Collection
    {
        // Users in the event's organization (either current org or membership) with permission
        $organizationId = $event->getOrganizationId();

        return User::where(function ($q) use ($organizationId) {
                $q->where('current_organization_id', $organizationId)
                  ->orWhereHas('organizations', function ($q2) use ($organizationId) {
                      $q2->where('organizations.id', $organizationId);
                  });
            })
            ->whereHas('roles', function ($query) {
                $query->whereHas('permissions', function ($q) {
                    $q->where('name', 'receive-event-notifications');
                });
            })
            ->get();
    }
}
