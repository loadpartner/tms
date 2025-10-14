<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;


class UpdateShipmentGeneral
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        ?float $weight = null,
        ?float $tripDistance = null,
        ?int $trailerTypeId = null,
        ?int $trailerSizeId = null,
        ?float $trailerTemperature = null,
        ?float $trailerTemperatureMaximum = null,
        ?bool $trailerTemperatureRange = false,
    ): Shipment {
        $shipment->update([
            'weight' => $weight,
            'trip_distance' => $tripDistance,
            'trailer_type_id' => $trailerTypeId,
            'trailer_size_id' => $trailerSizeId,
            'trailer_temperature' => $trailerTemperature,
            'trailer_temperature_maximum' => $trailerTemperatureMaximum,
            'trailer_temperature_range' => $trailerTemperatureRange ?? false,
        ]);


        return $shipment;
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->weight,
            $request->trip_distance,
            $request->trailer_type_id,
            $request->trailer_size_id,
            $request->trailer_temperature,
            $request->trailer_temperature_maximum,
            $request->trailer_temperature_range,
        );

        return redirect()->back()->with('success', 'Shipment updated successfully');
    }

    public function rules(): array
    {
        return [
            'trailer_temperature' => ['nullable', 'numeric'],
            'trailer_temperature_maximum' => [
                'nullable',
                'numeric',
                'required_if:trailer_temperature_range,true',
                function ($attribute, $value, $fail) {
                    $minTemp = request('trailer_temperature');
                    $isRange = request('trailer_temperature_range', false);
                    
                    if ($isRange && $minTemp !== null && $value !== null && $value <= $minTemp) {
                        $fail('The maximum temperature must be greater than the minimum temperature.');
                    }
                },
            ],
            'trailer_temperature_range' => ['boolean'],
            'trailer_type_id' => ['nullable', 'exists:trailer_types,id'],
            'trailer_size_id' => ['nullable', 'exists:trailer_sizes,id'],
            'weight' => ['nullable', 'numeric'],
            'trip_distance' => ['nullable', 'numeric'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
