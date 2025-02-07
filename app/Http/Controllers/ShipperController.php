<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreShipperRequest;
use App\Http\Requests\UpdateShipperRequest;
use App\Http\Resources\ShipperResource;
use App\Models\Shipper;
use Inertia\Inertia;
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
        return Inertia::render('Shippers/Index', [
            'shippers' => Shipper::query()
                ->when(request('search'), function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%");
                })
                ->paginate(10)
                ->withQueryString()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Shippers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mc_number' => 'nullable|string|max:255',
            'dot_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Shipper::create($validated);

        return redirect()->route('shippers.index')->with('success', 'Shipper created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipper $shipper)
    {
        return Inertia::render('Shippers/Show', [
            'shipper' => $shipper->load(['locations', 'contacts', 'notes', 'documents'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipper $shipper)
    {
        return Inertia::render('Shippers/Edit', [
            'shipper' => $shipper
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipper $shipper)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mc_number' => 'nullable|string|max:255',
            'dot_number' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $shipper->update($validated);

        return redirect()->route('shippers.index')->with('success', 'Shipper updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipper $shipper)
    {
        $shipper->delete();
        return redirect()->route('shippers.index')->with('success', 'Shipper deleted successfully.');
    }

    public function search(Request $request)
    {
        return Shipper::query()
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();
    }
}
