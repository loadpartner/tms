<?php

namespace App\Notifications;

use App\Contracts\Events\TmsEventContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private TmsEventContract $event
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $eventData = $this->event->getEventData();
        $eventType = $this->event->getEventType();
        
        return (new MailMessage)
            ->subject($this->getSubject($eventType))
            ->greeting("Hello {$notifiable->name},")
            ->line($this->getDescription($eventType, $eventData))
            ->action('View Details', $this->getActionUrl($eventType, $eventData))
            ->line('Thank you for using LoadPartner TMS!');
    }

    public function toArray($notifiable): array
    {
        return [
            'event_id' => $this->event->getEventId(),
            'event_type' => $this->event->getEventType(),
            'data' => $this->event->getEventData(),
            'occurred_at' => $this->event->getOccurredAt()->format('Y-m-d H:i:s'),
        ];
    }

    protected function getSubject(string $eventType): string
    {
        $subjects = [
            'shipment.created' => 'New Shipment Created',
            'shipment.state_changed' => 'Shipment Status Updated',
            'carrier.assigned' => 'Carrier Assigned to Shipment',
            'shipment.carrier_bounced' => 'Carrier Bounced from Shipment',
            'document.expired' => 'Document Expired',
        ];

        return $subjects[$eventType] ?? 'LoadPartner TMS Event Notification';
    }

    protected function getDescription(string $eventType, array $data): string
    {
        $descriptions = [
            'shipment.created' => "A new shipment {$data['shipment_number']} has been created.",
            'shipment.state_changed' => "Shipment {$data['shipment_number']} status changed from {$data['previous_state']} to {$data['current_state']}.",
            'carrier.assigned' => "Carrier {$data['carrier_name']} has been assigned to shipment {$data['shipment_number']}.",
            'shipment.carrier_bounced' => "Carrier {$data['carrier_name']} has been bounced from shipment {$data['shipment_number']}.",
            'document.expired' => "A document has expired and requires your attention.",
        ];

        return $descriptions[$eventType] ?? 'An event has occurred in your organization.';
    }

    protected function getActionUrl(string $eventType, array $data): string
    {
        $baseUrl = config('app.url');
        
        if (str_starts_with($eventType, 'shipment.') && isset($data['shipment_id'])) {
            return "{$baseUrl}/shipments/{$data['shipment_id']}";
        }
        
        if (str_starts_with($eventType, 'carrier.') && isset($data['carrier_id'])) {
            return "{$baseUrl}/carriers/{$data['carrier_id']}";
        }
        
        return "{$baseUrl}/dashboard";
    }
}
