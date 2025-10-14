<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use App\Traits\HandlesAuditHistory;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetShipmentAuditHistory
{
    use AsAction;
    use HandlesAuditHistory;

    public function handle(Shipment $shipment)
    {
        $audits = $this->getAuditHistory($shipment);
        return $this->formatAuditData($audits);
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $audits = $this->handle($shipment);
        return $this->jsonResponse($audits);
    }

    public function jsonResponse($audits)
    {
        return response()->json([
            'audits' => $audits,
        ]);
    }

    public function authorize(ActionRequest $request, Shipment $shipment): bool
    {
        return $request->user()->can('view', $shipment);
    }

    protected function getModelSpecificFieldMappings(string $auditableType): array
    {
        if ($auditableType === Shipment::class) {
            return [
                'organization_id' => 'Organization',
                'carrier_id' => 'Carrier',
                'customer_id' => 'Customer',
                'customer_name' => 'Customer',
                'driver_id' => 'Driver',
                'weight' => 'Weight',
                'trip_distance' => 'Trip Distance',
                'trailer_type_id' => 'Trailer Type',
                'trailer_size_id' => 'Trailer Size',
                'trailer_temperature_range' => 'Temperature Controlled',
                'trailer_temperature' => 'Temperature',
                'trailer_temperature_maximum' => 'Maximum Temperature',
                'shipment_number' => 'Shipment Number',
                'state' => 'Status',
            ];
        }

        if ($auditableType === ShipmentStop::class) {
            return [
                'facility_id' => 'Facility',
                'stop_type' => 'Stop Type',
                'special_instructions' => 'Special Instructions',
                'reference_numbers' => 'Reference Numbers',
                'stop_number' => 'Stop Number',
                'eta' => 'ETA',
                'arrived_at' => 'Arrived At',
                'loaded_unloaded_at' => 'Loaded/Unloaded At',
                'left_at' => 'Left At',
                'appointment_at' => 'Appointment Start',
                'appointment_end_at' => 'Appointment End',
                'appointment_type' => 'Appointment Type',
            ];
        }

        return [];
    }
}