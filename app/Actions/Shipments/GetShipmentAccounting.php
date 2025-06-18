<?php


namespace App\Actions\Shipments;

use App\Http\Resources\ShipmentAccountingResource;
use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShipmentAccounting {
    use AsAction;

    public function handle(Shipment $shipment) : Shipment
    {
        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        return $this->handle($shipment);
    }

    public function jsonResponse(Shipment $shipment)
    {
        return ShipmentAccountingResource::make(
            $shipment->load('payables', 'receivables')
        );
    }

    public function htmlResponse(Shipment $shipment)
    {
        return response('No view available', 404);
    }
}