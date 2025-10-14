<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Event Types for Notifications
    |--------------------------------------------------------------------------
    |
    | These event types will trigger notifications to users based on their
    | preferences and permissions.
    |
    */
    'notifiable' => [
        'shipment.created',
        'shipment.state_changed',
        'shipment.carrier_bounced',
        'carrier.assigned',
        'carrier.unassigned',
        'carrier.created',
        'carrier.status_changed',
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Retention
    |--------------------------------------------------------------------------
    |
    | How long to keep events in the database before they are automatically
    | cleaned up. Value is in days.
    |
    */
    'retention_days' => env('EVENT_RETENTION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for real-time event broadcasting.
    |
    */
    'broadcasting' => [
        'enabled' => env('EVENT_BROADCASTING_ENABLED', false),
        'driver' => env('BROADCAST_DRIVER', 'null'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Storage
    |--------------------------------------------------------------------------
    |
    | Whether to store events in the database for audit trails.
    |
    */
    'store_events' => env('STORE_EVENTS', true),

    /*
    |--------------------------------------------------------------------------
    | Async Processing
    |--------------------------------------------------------------------------
    |
    | Whether to process events asynchronously via queues.
    |
    */
    'async_processing' => env('EVENT_ASYNC_PROCESSING', true),
];