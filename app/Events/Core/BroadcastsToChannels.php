<?php

namespace App\Events\Core;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;

trait BroadcastsToChannels
{
    public function getBroadcastChannels(): array
    {
        $channels = [
            new PrivateChannel("organization.{$this->organizationId}"),
        ];

        // Add entity-specific channels based on event data
        $eventData = $this->getEventData();
        
        if (isset($eventData['shipment_id'])) {
            $channels[] = new PrivateChannel("shipment.{$eventData['shipment_id']}");
        }
        
        if (isset($eventData['carrier_id'])) {
            $channels[] = new PrivateChannel("carrier.{$eventData['carrier_id']}");
        }
        
        if (isset($eventData['customer_id'])) {
            $channels[] = new PrivateChannel("customer.{$eventData['customer_id']}");
        }
        
        if (isset($eventData['user_id'])) {
            $channels[] = new PrivateChannel("user.{$eventData['user_id']}");
        }

        return $channels;
    }

    public function broadcastOn(): array
    {
        return $this->getBroadcastChannels();
    }

    public function broadcastAs(): string
    {
        return $this->getEventType();
    }
}