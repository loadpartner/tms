<?php

namespace App\Http\Controllers\CheckCalls;

use App\Http\Controllers\Controller;
use App\Http\Resources\CheckCallResource;
use App\Models\CheckCalls\CheckCall;
use App\Models\Shipments\Shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CheckCallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Shipment $shipment)
    {   
        Gate::authorize('view', $shipment);
        return CheckCallResource::collection(
            $shipment->check_calls
                ->sortByDesc('created_at')
                ->load('note', 'creator')
        );
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment, CheckCall $checkcall)
    {
        Gate::authorize('delete', $checkcall);
        
        $checkcall->delete();

        return redirect()->back()->with('success', 'Check call deleted successfully');
    }
} 