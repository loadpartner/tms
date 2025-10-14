<?php

namespace App\Actions\Carriers;

use App\Http\Resources\Carriers\CarrierResource;
use App\Models\Carriers\Carrier;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCarrier
{
    use AsAction;

    public function handle(
        string $name,
    ): Carrier
    {
        $carrier = Carrier::create([
            'organization_id' => current_organization_id(),
            'name' => $name,
        ]);

        return $carrier;
    }

    public function asController(ActionRequest $request): Carrier
    {
        return $this->handle(
            name: $request->validated('name'),
        );
    }

    public function jsonResponse(Carrier $carrier)
    {
        return CarrierResource::make($carrier);
    }

    public function htmlResponse(Carrier $carrier)
    {
        return redirect()->route('carriers.show', $carrier);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::CARRIER_EDIT);
    }
}
