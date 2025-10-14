<?php

namespace App\Actions\Shipments;

use App\Enums\StopType;
use App\Enums\TemperatureUnit;
use App\Events\Shipments\ShipmentCarrierUpdated;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CreateShipment
{
    use AsAction;

    public function handle(
        array $customerIds,
        array $stops,
        ?int $carrierId = null,
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
        // Check load limits for startup plans
        try {
            \App\Actions\Organizations\CheckShipmentLimits::run(current_organization());
        } catch (BadRequestException $e) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'shipment_limit' => [$e->getMessage()]
            ]);
        }

        $organizationId = current_organization_id();

        if (is_null($organizationId)) {
            throw new \RuntimeException('No organization context available when creating a shipment.');
        }

        $shipment = DB::transaction(function () use (
            $customerIds,
            $stops,
            $carrierId,
            $weight,
            $tripDistance,
            $trailerTypeId,
            $trailerSizeId,
            $trailerTemperatureRange,
            $trailerTemperature,
            $trailerTemperatureMaximum,
            $shipmentNumber,
            $organizationId,
        ) {
            $shipment = Shipment::create([
                'organization_id' => $organizationId,
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

            $customerPivotData = [];

            foreach ($customerIds as $customerId) {
                $customerPivotData[$customerId] = [
                    'organization_id' => $organizationId,
                ];
            }

            $shipment->customers()->attach($customerPivotData);

            foreach ($stops as $stopData) {
                $shipment->stops()->create([
                    'organization_id' => $organizationId,
                    'facility_id' => $stopData['facility_id'],
                    'stop_type' => $stopData['stop_type'],
                    'stop_number' => $stopData['stop_number'],
                    'special_instructions' => $stopData['special_instructions'],
                    'reference_numbers' => $stopData['reference_numbers'],
                    'appointment_at' => Carbon::parse($stopData['appointment_at']),
                ]);
            }

            return $shipment;
        });

        if ($carrierId) {
            event(new ShipmentCarrierUpdated($shipment));
        }

        return $shipment;
    }

    public function asController(ActionRequest $request) : Shipment 
    {
        return $this->handle(
            customerIds: $request->validated('customer_ids'),
            carrierId: $request->validated('carrier_id'),
            stops: $request->validated('stops'),
            weight: $request->validated('weight'),
            tripDistance: $request->validated('trip_distance'),
            trailerTypeId: $request->validated('trailer_type_id'),
            trailerSizeId: $request->validated('trailer_size_id'),
            trailerTemperatureRange: $request->validated('trailer_temperature_range'),
            trailerTemperature: $request->validated('trailer_temperature'),
            trailerTemperatureMaximum: $request->validated('trailer_temperature_maximum'),
            shipmentNumber: $request->validated('shipment_number'),
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

            'carrier_id' => ['exists:carriers,id'],
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

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
