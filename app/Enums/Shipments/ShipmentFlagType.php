<?php

namespace App\Enums\Shipments;

enum ShipmentFlagType: string
{
    case TRUCK_IS_LATE = 'truck-is-late';
    case TRUCK_IS_EMPTY = 'truck-is-empty';

    public function getLabel(): string
    {
        return match ($this) {
            self::TRUCK_IS_LATE => 'Truck is late',
            self::TRUCK_IS_EMPTY => 'Truck is empty',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::TRUCK_IS_LATE => 'red',
            self::TRUCK_IS_EMPTY => 'yellow',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::TRUCK_IS_LATE => 'timer',
            self::TRUCK_IS_EMPTY => 'square-dashed',
        };
    }
}