<?php

namespace App\Actions\Accounting;

use App\Enums\Accounting\Currency;
use App\Facades\AliasResolver;
use App\Http\Resources\Accounting\PayableResource;
use App\Models\Accounting\Payable;
use App\Models\Shipments\Shipment;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SavePayables 
{
    use AsAction;

    public function handle(Shipment $shipment, array $payables) : Collection 
    {
        // Extract IDs from the rates array
        $payableIds = collect($payables)
            ->pluck('id')
            ->filter()
            ->toArray();
        $toDelete = $shipment->payables()->whereNotIn('id', $payableIds)->get();
        // Delete rates that are no longer in the request
        foreach ($toDelete as $payable) {
            $payable->delete();
        }
        
        // Create or update rates
        $updatedPayables = collect();
        foreach ($payables as $payableData) {

            $calculatedTotal = $payableData['quantity'] * $payableData['rate'];   
            // If there's a discrepancy, adjust the rate to match the total
            if (abs($calculatedTotal - $payableData['total']) > 0.01) {
                $payableData['quantity'] = max($payableData['quantity'], 1);
                $payableData['rate'] = $payableData['total'] / $payableData['quantity'];
            }

            if (isset($payableData['id'])) {
                $payable = Payable::where('id', $payableData['id'])->where('shipment_id', $shipment->id)->first();
                if ($payable) {
                    $payable->update($payableData);
                    $updatedPayables->push($payable->fresh());
                    continue;   // continue, we created and are g2g
                }
            }

            $payable = new Payable($payableData);
            $payable->shipment_id = $shipment->id;
            $payable->save();
            $updatedPayables->push($payable);
        }
        
        return $updatedPayables;

    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $payables = $request->validated('payables', []);

        foreach($payables as $index => $payable) {
            $payables[$index]['payee_type'] = AliasResolver::getModelClass(
                $payables[$index]['payee_type']
            );
        }

        return $this->handle(
            $shipment,
            $payables,
        );
    }

    public function htmlResponse(Collection $payables)
    {
        return redirect()->back();
    }

    public function jsonResponse(Collection $payables)
    {
        return PayableResource::collection($payables);
    }

    public function rules() 
    {
        return [
            'payables.*.id' => 'nullable',
            'payables.*.payee_id' => 'required',
            'payables.*.payee_type' => 'required|string',
            'payables.*.rate' => 'required|numeric',
            'payables.*.quantity' => 'required|numeric',
            'payables.*.total' => 'required|numeric',
            'payables.*.rate_type_id' => 'required|exists:rate_types,id',
            'payables.*.currency_code' => ['required', Rule::enum(Currency::class)]
        ];
    }
} 