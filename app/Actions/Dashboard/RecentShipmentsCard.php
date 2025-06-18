<?php

namespace App\Actions\Dashboard;

use App\Http\Resources\ShipmentResource;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class RecentShipmentsCard 
{
    use AsAction;

    public function handle() : Collection
    {
        $shipments = ShipmentStop::where(function(Builder $builder) {
                $builder->where('appointment_at', '>=', now()->addHours(-4))
                    ->where('appointment_at', '<=', now()->addDay());
            })
            ->orWhere(function(Builder $builder) {
                $builder->where('eta', '>=', now()->addHours(-4))
                    ->where('eta', '<=', now()->addDay());
            })
            ->with('shipment', 'shipment.stops')
            ->orderBy('appointment_at')
            ->limit(5)
            ->get()
            ->pluck('shipment');
        return $shipments;
    }

    public function asController()
    {
        return $this->handle();
    }

    public function jsonResponse(Collection $shipments)
    {
        return response()->json(ShipmentResource::collection($shipments));
        
    }

}