<?php

namespace App\Actions\Shipments;

use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Nette\NotImplementedException;

class CreateShipment
{
    use AsAction;

    public function handle(array $shipperIds, int $carrierId, array $stops): Shipment
    {

        DB::beginTransaction();

        $shipment = Shipment::create([
            'carrier_id' => $carrierId,
        ]);

        $shipment->shippers()->attach($shipperIds);

        foreach ($stops as $stopData) {
            $stop = $shipment->stops()->create([
                'facility_id' => $stopData['facility_id'],
                'stop_type' => $stopData['stop_type'],
            ]);

            $stop->appointment()->create([
                'appointment_datetime' => Carbon::parse($stopData['appointment']['datetime']),
            ]);
        }

        DB::commit();

        return $shipment;
    }
}
