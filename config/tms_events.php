<?php

use App\Events\Carriers\CarrierAssigned;
use App\Events\Carriers\CarrierCreated;
use App\Events\Carriers\CarrierStatusChanged;
use App\Events\Carriers\CarrierUnassigned;
use App\Events\Customers\CustomerAssigned;
use App\Events\Customers\CustomerUnassigned;
use App\Events\Shipments\ShipmentCarrierBounced;
use App\Events\Shipments\ShipmentCreated;
use App\Events\Shipments\ShipmentDeleted;
use App\Events\Shipments\ShipmentRestored;
use App\Events\Shipments\ShipmentStateChanged;
use App\Events\Shipments\ShipmentStateChangedTms;
use App\Events\Shipments\ShipmentUpdated;
use App\Listeners\Events\AuditListener;
use App\Listeners\Events\MetricsListener;
use App\Listeners\Events\NotificationListener;
use App\Listeners\Shipments\RelayShipmentStateChanged;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;

return [
    'listeners' => [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ShipmentCreated::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        ShipmentUpdated::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        ShipmentDeleted::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        ShipmentRestored::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        ShipmentStateChanged::class => [
            RelayShipmentStateChanged::class,
        ],
        ShipmentStateChangedTms::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        CarrierAssigned::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        CarrierUnassigned::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        CarrierCreated::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        CarrierStatusChanged::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        ShipmentCarrierBounced::class => [
            AuditListener::class,
            MetricsListener::class,
            NotificationListener::class,
        ],
        CustomerAssigned::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
        CustomerUnassigned::class => [
            AuditListener::class,
            MetricsListener::class,
        ],
    ],
    'audit' => [
        'tracked_events' => [
            'shipment.created',
            'shipment.updated',
            'shipment.deleted',
            'shipment.restored',
            'shipment.state_changed',
            'shipment.carrier_bounced',
            'carrier.assigned',
            'carrier.unassigned',
            'carrier.created',
            'carrier.status_changed',
            'customer.assigned',
            'customer.unassigned',
            'payable.created',
            'payable.updated',
            'payable.deleted',
            'receivable.created',
            'receivable.updated',
            'receivable.deleted',
            'document.uploaded',
            'document.deleted',
            'document.expired',
            'user.permission_changed',
            'organization.settings_changed',
        ],
    ],
    'metrics' => [
        'handlers' => [
            'shipment.created' => 'incrementShipmentMetrics',
            'shipment.state_changed' => 'updateShipmentStateMetrics',
            'carrier.assigned' => 'updateCarrierMetrics',
            'payable.created' => 'updateFinancialMetrics',
            'receivable.created' => 'updateFinancialMetrics',
        ],
    ],
];
