<?php

namespace App\States\Shipments;

use App\Events\Shipments\ShipmentStateChanged;
use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ShipmentState extends State
{

    abstract public function label(): string;
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->stateChangedEvent(ShipmentStateChanged::class)
            ->allowTransitions(
                [
                    // Support for normal happy path
                    [Pending::class, Booked::class],
                    [Booked::class, Dispatched::class],
                    [Dispatched::class, AtPickup::class],
                    [AtPickup::class, InTransit::class],
                    [InTransit::class, AtDelivery::class],
                    [AtDelivery::class, Delivered::class],

                    // Support for bounced carrier transitions
                    [
                        [
                            Booked::class,
                            Dispatched::class,
                            AtPickup::class,
                        ],
                        Pending::class
                    ],

                    // Support for canceled transitions
                    [
                        [
                            Pending::class,
                            Booked::class,
                            Dispatched::class,
                            AtPickup::class,
                            InTransit::class,
                            AtDelivery::class,
                            Delivered::class,
                        ],
                        Canceled::class
                    ],
                ]
            )
        ;
    }
}
