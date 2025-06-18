<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\Customers\CustomerResource;
use App\Http\Resources\FacilityResource;
use App\Models\Customers\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CustomerController extends ResourceSearchController
{
    protected $model = Customer::class;
    protected $modelResource = CustomerResource::class;
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize(\App\Enums\Permission::CUSTOMER_VIEW);
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
        ]);

        if ($request->input('customer_id')) {
            $customer = Customer::find($request->input('customer_id'));
            return Inertia::render('Customers/Index', [
                'customer' => CustomerResource::make($customer),
            ]);
        }

        return Inertia::render('Customers/Index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        Gate::authorize(\App\Enums\Permission::CUSTOMER_VIEW);
        return Inertia::render('Customers/Show', [
            'customer' => CustomerResource::make($customer),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }

    public function facilities(Customer $customer)
    {
        return FacilityResource::collection($customer->facilities->load('location'));
    }
}
