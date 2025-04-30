<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use App\States\Shipments\Canceled;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CancelShipment
{
    use AsAction;

    public function handle(
        Shipment $shipment,
    ): Shipment {

        $shipment->state->transitionTo(Canceled::class);

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
        );

        return redirect()->back()->with('success', 'Shipment canceled successfully');
    }

    public function rules(): array
    {
        return [
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
