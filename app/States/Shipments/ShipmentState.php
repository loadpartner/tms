<?php

namespace App\States\Shipments;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class ShipmentState extends State
{

    abstract public function label(): string;
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransitions(
                [
                    [Pending::class, Booked::class],
                    [Booked::class, Dispatched::class],
                    [Dispatched::class, AtPickup::class],
                    [AtPickup::class, InTransit::class],
                    [InTransit::class, AtDelivery::class],
                    [AtDelivery::class, Delivered::class],

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
