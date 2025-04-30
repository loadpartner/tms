<?php

namespace App\Actions\Shipments;

use App\Enums\Shipments\ShipmentFlagType;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentFlag;
use App\States\Shipments\Canceled;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class DeleteShipmentFlag
{
    use AsAction;

    public function handle(
        ShipmentFlag $shipmentFlag,
    ): ShipmentFlag {

        $shipmentFlag->delete();

        return $shipmentFlag;
    }

    public function asController(ActionRequest $request, Shipment $shipment, ShipmentFlag $shipmentFlag)
    {
        $this->handle(
            $shipmentFlag
        );

        return redirect()->back()->with('success', 'Shipment flag deleted successfully');
    }

    public function rules(): array
    {
        return [];
    }
}
