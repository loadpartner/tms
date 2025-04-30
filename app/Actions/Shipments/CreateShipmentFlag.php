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

class CreateShipmentFlag
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        string $type,
    ): ShipmentFlag {

        return $shipment->shipment_flags()->create([
            'type' => $type,
            'created_by' => Auth::user()->id,
            'updated_by' => Auth::user()->id,
        ]);
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->validated('type')
        );

        return redirect()->back()->with('success', 'Shipment flag created successfully');
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!ShipmentFlagType::tryFrom($value) && 
                    !\App\Models\Shipments\ShipmentFlagCustomType::where('name', $value)->exists()) {
                    $fail('The selected type is invalid. It must be a predefined flag type or a custom flag type.');
                }
            }],
        ];
    }
}
