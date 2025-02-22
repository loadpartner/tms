<?php

namespace App\Http\Controllers\Shipments;

use App\Actions\Shipments\CreateShipment;
use App\Actions\Shipments\UpdateShipmentNumber;
use App\Http\Requests\Shipments\StoreShipmentRequest;
use App\Http\Requests\Shipments\UpdateShipmentRequest;
use App\Models\Shipments\Shipment;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResourceSearchController;
use App\Http\Requests\ResourceSearchRequest;
use App\Http\Resources\NoteResource;
use App\Http\Resources\ShipmentResource;
use App\Http\Resources\ShipmentStopResource;
use App\Http\Resources\TrailerSizeResource;
use App\Http\Resources\TrailerTypeResource;
use App\Models\Carriers\Carrier;
use App\Models\Facility;
use App\Models\Shipments\TrailerSize;
use App\Models\Shipments\TrailerType;
use App\Models\Customers\Customer;
use Inertia\Inertia;
use App\Events\ShipmentCreated;
use Illuminate\Http\Request;

class ShipmentController extends ResourceSearchController
{
    protected $model = Shipment::class;
    protected $modelResource = ShipmentResource::class;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Shipments/Index');  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Shipments/Create', [
            'trailerTypes' => TrailerTypeResource::collection(TrailerType::all()),
            'trailerSizes' => TrailerSizeResource::collection(TrailerSize::all()),
        ]);
    }


    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        return Inertia::render('Shipments/Show', [
            'shipment' => $shipment->load('carrier', 'customers'),
            'stops' => ShipmentStopResource::collection($shipment->stops->load('facility')),
            'trailerTypes' => TrailerTypeResource::collection(TrailerType::all()),
            'trailerSizes' => TrailerSizeResource::collection(TrailerSize::all()),
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
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        //
    }

    public function store(Request $request)
    {
        $shipment = Shipment::create($request->all());

        event(new ShipmentCreated($shipment)); // Dispatch the event

        // ... rest of your logic, like returning a response ...
        return response()->json(['shipment' => $shipment], 201);
    }
}
