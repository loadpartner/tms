<?php

namespace App\Actions\Shipments;

use App\Enums\StopType;
use App\Events\Shipments\ShipmentStopsUpdated;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShipmentStops
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        array $stops,
    ): Shipment {

        ShipmentStop::where('shipment_id', $shipment->id)
            ->whereNotIn('id', array_column($stops, 'id'))
            ->delete();

        foreach ($stops as $stop) {
            $shipment->stops()->updateOrCreate(['id' => $stop['id']], $stop);
        }

        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->validated('stops'),
        );

        return redirect()->back()->with('success', 'Shipment stops updated');
    }

    public function rules(): array
    {
        return [
            'stops' => ['required', 'array'],
            'stops.*.id' => ['integer', 'nullable'],
            'stops.*.stop_number' => ['required', 'integer'],
            'stops.*.stop_type' => ['required', Rule::enum(StopType::class)],
            'stops.*.facility_id' => ['required', 'integer', 'exists:facilities,id'],
            'stops.*.special_instructions' => ['string', 'nullable'],
            'stops.*.reference_numbers' => ['string', 'nullable'],
            'stops.*.appointment_type' => ['string', 'nullable'],
            'stops.*.appointment_at' => ['date'],
            'stops.*.appointment_end_at' => ['date', 'nullable'],
            'stops.*.arrived_at' => ['date', 'nullable'],
            'stops.*.eta' => ['date', 'nullable'],
            'stops.*.loaded_unloaded_at' => ['date', 'nullable'],
            'stops.*.left_at' => ['date', 'nullable'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
