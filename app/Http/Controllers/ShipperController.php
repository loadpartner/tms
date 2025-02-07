<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreShipperRequest;
use App\Http\Requests\UpdateShipperRequest;
use App\Http\Resources\ShipmentResource;
use App\Http\Resources\ShipperResource;
use App\Models\Shipper;
use Illuminate\Http\Request;

class ShipperController extends ResourceSearchController
{
    protected $model = Shipper::class;
    protected $modelResource = ShipperResource::class;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Shippers/Index', [
            'shippers' => ShipperResource::collection(
                Shipper::where('organization_id', current_organization()->id)
                    ->orderBy('name')
                    ->get()
            ),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Shippers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreShipperRequest $request)
    {
        $shipper = Shipper::create([
            'organization_id' => current_organization()->id,
            'name' => $request->name,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'contact_name' => $request->contact_name,
            'phone' => $request->phone,
            'email' => $request->email,
        ]);

        return redirect()->route('shippers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipper $shipper)
    {
        return new ShipperResource($shipper->load(['locations']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipper $shipper)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateShipperRequest $request, Shipper $shipper)
    {
        $shipper->update($request->validated());

        return new ShipperResource($shipper);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipper $shipper)
    {
        $shipper->delete();

        return redirect()->route('shippers.index');
    }

    /**
     * Get loads for a shipper.
     */
    public function loads(Shipper $shipper, Request $request)
    {
        $query = $shipper->shipments()->with(['carrier']);

        if ($request->boolean('active_only', true)) {
            $query->whereNotIn('status', ['completed', 'cancelled']);
        }

        return ShipmentResource::collection($query->get());
    }
}
