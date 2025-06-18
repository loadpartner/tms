<?php

namespace App\Http\Controllers;

use App\Enums\Carriers\BounceCause;
use App\Http\Resources\Carriers\CarrierBounceResource;
use App\Http\Resources\Carriers\CarrierResource;
use App\Models\Carriers\Carrier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CarrierController extends ResourceSearchController
{
    protected $model = Carrier::class;
    protected $modelResource = CarrierResource::class;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize(\App\Enums\Permission::CARRIER_VIEW);
        return Inertia::render(
            'Carriers/Index',
            [
                'allowFmcsaSearch' => config('fmcsa.api_key') ? true : false,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Carrier $carrier)
    {
        Gate::authorize(\App\Enums\Permission::CARRIER_VIEW);
        return Inertia::render('Carriers/Show', [
            'carrier' => CarrierResource::make($carrier->load('physical_location', 'billing_location', 'safer_report')),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carrier $carrier)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carrier $carrier)
    {
        //
    }

    public function bouncedLoads(Carrier $carrier)
    {
        return response()->json(
            CarrierBounceResource::collection(
                $carrier->bounces
                    ->sortByDesc('created_at')
                    ->load('shipment')
                    ->load('bouncedBy')
                    ->load('driver')
            )
        );
    }

    public function bounceReasons()
    {
        $cases = BounceCause::cases();
        $cases = array_map(function ($case) {
            return $case->value;
        }, $cases);
        sort($cases);
        return response($cases);
    }
}
