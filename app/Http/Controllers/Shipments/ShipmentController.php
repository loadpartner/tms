<?php

namespace App\Http\Controllers\Shipments;

use App\Actions\Shipments\CreateShipment;
use App\Http\Requests\Shipments\StoreShipmentRequest;
use App\Http\Requests\Shipments\UpdateShipmentRequest;
use App\Models\Shipments\Shipment;
use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\Facility;
use App\Models\Shipper;
use Inertia\Inertia;

class ShipmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Shipments/Index', [
            'shipments' => Shipment::all(),
        ]);  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Shipments/Create', [
            'facilities' => Facility::all(),
            'shippers' => Shipper::all(),
            'carriers' => Carrier::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShipmentRequest $request)
    {

        // TODO - hack until multi support is added
        $shipperIds = [$request->shipper_ids];

        $shipment = CreateShipment::run(
            shipperIds: $shipperIds,
            carrierId: $request->carrier_id,
            stops: $request->stops,
        );

        return redirect()->route('shipments.show', $shipment);
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        return Inertia::render('Shipments/Show', [
            'shipment' => $shipment,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipmentRequest $request, Shipment $shipment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        //
    }
}
