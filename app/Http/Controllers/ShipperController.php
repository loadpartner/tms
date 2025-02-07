<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResourceSearchRequest;
use App\Http\Requests\StoreShipperRequest;
use App\Http\Requests\UpdateShipperRequest;
use App\Http\Resources\ShipperResource;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShipperController extends ResourceSearchController
{
    protected $model = Shipper::class;
    protected $modelResource = ShipperResource::class;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shippers = Shipper::all(); // Or paginate: Shipper::paginate(10);

        return Inertia::render('Shippers/Index', [
            'shippers' => $shippers,
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
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
        ]);

        $shipper = Shipper::create($validated);

        return redirect()->route('shippers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipper $shipper)
    {
        if (request()->wantsJson() || request()->hasHeader('X-Inertia-Partial-Data')) {
             return response()->json([
                'shipper' => $shipper, // Return just the shipper data
            ]);
        }

        // Initial full page load (shouldn't normally happen, but good to have)
        $shippers = Shipper::all();
        return Inertia::render('Shippers/Index', [
            'shippers' => $shippers,
            'shipper' => $shipper,
        ]);
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
    public function update(Request $request, Shipper $shipper)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            // Add other fields
        ]);

        $shipper->update($validated);

        return redirect()->back(); // Or wherever you want to redirect
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipper $shipper)
    {
        //
    }

    public function loads(Request $request, Shipper $shipper)
    {
        $activeOnly = $request->input('activeOnly', false) === 'true'; // Ensure boolean
        $searchTerm = $request->input('search', '');

        $loadsQuery = $shipper->loads(); // Assuming you have a `loads` relationship on your Shipper model

        if ($activeOnly) {
            $loadsQuery->where('status', 'active'); // Adjust 'active' to your actual status
        }

        if ($searchTerm) {
            $loadsQuery->where(function ($query) use ($searchTerm) {
                $query->where('id', 'like', '%' . $searchTerm . '%')
                      ->orWhere('status', 'like', '%' . $searchTerm . '%'); // Add other searchable fields
            });
        }


        $loads = $loadsQuery->get(); // Or paginate

        return response()->json(['loads' => $loads]);
    }

    public function storeLocation(Request $request, Shipper $shipper)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:10',
        ]);

        $location = $shipper->locations()->create($validated); // Assuming a `locations` relationship

        return redirect()->back();
    }

    public function storeNote(Request $request, Shipper $shipper)
    {
        $validated = $request->validate([
            'note' => 'required|string',
        ]);

        $note = $shipper->notes()->create($validated); // Assuming a `notes` relationship

        return redirect()->back();
    }
}
