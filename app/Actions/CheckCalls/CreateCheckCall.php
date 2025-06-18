<?php

namespace App\Actions\CheckCalls;

use App\Enums\ContactMethodType;
use App\Enums\Permission;
use App\Models\CheckCalls\CheckCall;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCheckCall
{
    use AsAction;

    public function handle(
        int $shipmentId,
        ?string $eta,
        ?int $reportedTrailerTemp,
        string $contactName,
        ?ContactMethodType $contactMethod,
        ?string $contactMethodDetail,
        ?bool $isLate,
        ?bool $isTruckEmpty,
        ?string $note,
        ?string $arrivedAt,
        ?string $leftAt,
        ?string $loadedUnloadedAt,
    ): CheckCall {
        $shipment = Shipment::findOrFail($shipmentId);
        $carrierId = $shipment->carrier_id;

        $noteId = null;
        if ($note) {
            $note = $shipment->notes()->create([
                'note' => $note,
            ]);
            $noteId = $note->id;
        }

        $nextStopId = $shipment->next_stop?->id;
        $currentStopId = $shipment->current_stop?->id;

        $checkCall = CheckCall::create([
            'organization_id' => Auth::user()->current_organization_id,
            'carrier_id' => $carrierId,
            'shipment_id' => $shipmentId,
            'eta' => $eta,
            'reported_trailer_temp' => $reportedTrailerTemp,
            'contact_name' => $contactName,
            'contact_method' => $contactMethod,
            'contact_method_detail' => $contactMethodDetail,
            'is_late' => $isLate,
            'is_truck_empty' => $isTruckEmpty,
            'note_id' => $noteId,
            'arrived_at' => $arrivedAt,
            'left_at' => $leftAt,
            'loaded_unloaded_at' => $loadedUnloadedAt,
            'user_id' => Auth::id(),
            'next_stop_id' => $nextStopId,
            'current_stop_id' => $currentStopId,
        ]);

        $this->reflectCheckCallDetails($checkCall);


        return $checkCall;
    }

    public function asController(ActionRequest $request, Shipment $shipment): CheckCall
    {
        $validatedData = $request->validated();

        $contactMethod = $validatedData['contact_method'] ? ContactMethodType::from($validatedData['contact_method']) : null;
        return $this->handle(
            shipmentId: $shipment->id,
            eta: $validatedData['eta'] ?? null,
            reportedTrailerTemp: $validatedData['reported_trailer_temp'] ?? null,
            contactName: $validatedData['contact_name'] ?? null,
            contactMethod: $contactMethod,
            contactMethodDetail: $validatedData['contact_method_detail'] ?? null,
            isLate: $validatedData['is_late'] ?? null,
            isTruckEmpty: $validatedData['is_truck_empty'] ?? null,
            note: $validatedData['note'] ?? null,
            arrivedAt: $validatedData['arrived_at'] ?? null,
            leftAt: $validatedData['left_at'] ?? null,
            loadedUnloadedAt: $validatedData['loaded_unloaded_at'] ?? null,
        );
    }

    public function rules(): array
    {
        return [
            'eta' => ['nullable', 'date'],
            'reported_trailer_temp' => ['nullable', 'numeric'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_method' => ['nullable', 'string', 'max:255'],
            'contact_method_detail' => ['nullable', 'string', 'max:255'],
            'is_late' => ['nullable', 'boolean'],
            'is_truck_empty' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string', 'max:255'],
            'arrived_at' => ['nullable', 'date'],
            'left_at' => ['nullable', 'date'],
            'loaded_unloaded_at' => ['nullable', 'date'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return Auth::user()->can(Permission::CHECK_CALL_EDIT);
    }

    public function jsonResponse(CheckCall $checkCall)
    {
        return response()->json([
            'message' => 'Check call created successfully',
            'check_call' => new \App\Http\Resources\CheckCallResource($checkCall),
        ]);
    }

    public function htmlResponse(CheckCall $checkCall)
    {
        return redirect()->back()->with('success', 'Check call created successfully');
    }


    private function reflectCheckCallDetails(CheckCall $checkCall)
    {        
        // Updates related to next stop
        $nextStop = $checkCall->nextStop;
        $nextStopUpdates = [];
        // Update arrival time if provided in the check call
        if ($checkCall->arrived_at) {
            $nextStopUpdates['arrived_at'] = $checkCall->arrived_at;
        }
        if ($checkCall->eta) {
            $nextStopUpdates['eta'] = $checkCall->eta;
        }

        if ($nextStopUpdates) {
            $nextStop->update($nextStopUpdates);
        }

        // Updates related to current stop
        $currentStop = $checkCall->currentStop;
        $currentStopUpdates = [];
        // Update departure time if provided in the check call
        if ($checkCall->left_at) {
            $currentStopUpdates['left_at'] = $checkCall->left_at;
        }

        // Update loaded/unloaded time if provided in the check call
        if ($checkCall->loaded_unloaded_at) {
            $currentStopUpdates['loaded_unloaded_at'] = $checkCall->loaded_unloaded_at;
        }

        if ($currentStopUpdates) {
            $currentStop->update($currentStopUpdates);
        }
    }
}
