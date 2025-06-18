<?php

namespace App\Events\Shipments;

use Spatie\ModelStates\Events\StateChanged;

class ShipmentStateChanged extends StateChanged
{
    // The parent StateChanged event already provides all the necessary properties:
    // - public $model (the model instance)
    // - public $initialState (the initial state)
    // - public $finalState (the final state)  
    // - public $transition (the transition instance)
    
    // Additional methods or properties specific to shipment state changes 
    // can be added here if needed
}
