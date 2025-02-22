<?php

namespace App\Actions\Shipments;

use App\Enums\StopType;
use App\Enums\TemperatureUnit;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;
use App\Events\ShipmentCreated;
use Lorisleiva\Actions\Concerns\AsJob;

class CreateShipment
{
    use AsAction, AsJob;

    public function handle(
        array $customerIds,
        int $carrierId,
        array $stops,
        ?float $weight = null,
        ?float $tripDistance = null,
        ?int $trailerTypeId = null,
        ?int $trailerSizeId = null,
        ?bool $trailerTemperatureRange = false,
        ?float $trailerTemperature = null,
        ?float $trailerTemperatureMaximum = null,
        ?string $shipmentNumber = null,
    ): Shipment
    {
        DB::beginTransaction();

        $shipment = Shipment::create([
            'carrier_id' => $carrierId,
            'weight' => $weight,
            'trip_distance' => $tripDistance,
            'trailer_type_id' => $trailerTypeId,
            'trailer_size_id' => $trailerSizeId,
            'trailer_temperature_range' => $trailerTemperatureRange ?? false,
            'trailer_temperature' => $trailerTemperature,
            'trailer_temperature_maximum' => $trailerTemperatureMaximum,
            'shipment_number' => $shipmentNumber,
        ]);

        $shipment->customers()->attach($customerIds);

        foreach ($stops as $stopData) {
            $stop = $shipment->stops()->create([
                'facility_id' => $stopData['facility_id'],
                'stop_type' => $stopData['stop_type'],
                'stop_number' => $stopData['stop_number'],
                'special_instructions' => $stopData['special_instructions'],
                'reference_numbers' => $stopData['reference_numbers'],
                'appointment_at' => Carbon::parse($stopData['appointment_at']),
            ]);
        }

        event(new ShipmentCreated($shipment));

        DB::commit();

        return $shipment;
    }

    public function asController(ActionRequest $request) : Shipment 
    {
        return $this->handle(
            customerIds: $request->customer_ids,
            carrierId: $request->carrier_id,
            stops: $request->stops,
            weight: $request->weight,
            tripDistance: $request->trip_distance,
            trailerTypeId: $request->trailer_type_id,
            trailerSizeId: $request->trailer_size_id,
            trailerTemperatureRange: $request->trailer_temperature_range,
            trailerTemperature: $request->trailer_temperature,
            trailerTemperatureMaximum: $request->trailer_temperature_maximum,
            shipmentNumber: $request->shipment_number,
        );
    }

    public function htmlResponse(Shipment $shipment)
    {
        return redirect()->route('shipments.show', $shipment);
    }

    public function jsonResponse(Shipment $shipment)
    {
        return response()->json(ShipmentResource::make($shipment));
    }

    public function rules()
    {
        return [
            'customer_ids' => ['required', 'array'],
            'customer_ids.*' => ['required', 'exists:customers,id'],

            'shipment_number' => ['nullable', 'string'],

            'weight' => ['nullable', 'numeric'],
            'trip_distance' => ['nullable', 'numeric'],
            'trailer_type_id' => ['nullable', 'exists:trailer_types,id'],
            'trailer_size_id' => ['nullable', 'exists:trailer_sizes,id'],
            'trailer_temperature_range' => ['nullable', 'boolean'],
            'trailer_temperature' => ['nullable', 'numeric'],
            'trailer_temperature_maximum' => ['nullable', 'numeric'],

            'carrier_id' => ['required', 'exists:carriers,id'],
            'stops' => ['required', 'array'],
            'stops.*.stop_type' => ['required', Rule::enum(StopType::class)],
            'stops.*.facility_id' => ['required', 'exists:facilities,id'],
            'stops.*.appointment_at' => ['required', 'date'],
            'stops.*.appointment_end_at' => ['date'],
            'stops.*.appointment_type' => ['string'],
            'stops.*.special_instructions' => ['nullable', 'string'],
            'stops.*.reference_numbers' => ['nullable', 'string'],
            'stops.*.stop_number' => ['required', 'integer'],
        ];
    }
}
