<?php

namespace App\Actions\Dashboard;

use App\Http\Resources\Carriers\CarrierResource;
use App\Models\Shipments\Shipment;
use App\States\Shipments\Booked;
use App\States\Shipments\ShipmentState;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class RecentCarriersCard 
{
    use AsAction;

    public function handle() : Collection
    {
        $carriers = Shipment::whereState('state', Booked::class)
            ->whereNotNull('carrier_id')
            ->latest()->limit(5)
            ->with('carrier')
            ->get()
            ->pluck('carrier');

        return $carriers;
    }

    public function asController()
    {
        return $this->handle();
    }

    public function jsonResponse(Collection $carriers)
    {
        return response()->json(CarrierResource::collection($carriers));
        
    }

}