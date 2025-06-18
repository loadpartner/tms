<?php
declare(strict_types=1);
namespace App\Enums;

use App\Models\Carriers\Carrier;
use App\Models\Customers\Customer;
use App\Models\Facility;
use App\Models\CheckCalls\CheckCall;
use App\Models\Shipments\Shipment;

enum Notable: string
{
    case SHIPMENT = 'shipment';
    case CUSTOMER = 'customer';
    case CARRIER = 'carrier';
    case FACILITY = 'facility';
    case CHECK_CALL = 'check_call';

    public function getClassName(): string
    {
        return match ($this) {
            self::SHIPMENT => Shipment::class,
            self::CUSTOMER => Customer::class,
            self::CARRIER => Carrier::class,
            self::FACILITY => Facility::class,
            self::CHECK_CALL => CheckCall::class,
        };
    }
}