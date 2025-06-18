<?php

namespace App\Actions\Subscriptions;

use App\Enums\Permission;
use App\Enums\Subscriptions\SubscriptionType;
use App\Models\Shipments\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Laravel\Cashier\Subscription;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateUserSeatsSubscription
{
    use AsAction;

    public function handle(
        int $quantity = 0
    )
    {
        // Check if billing is enabled
        if (!config('subscriptions.enable_billing')) {
            abort(403, 'Billing is disabled');
        }

        $organization = current_organization();

        if($organization->subscribed(SubscriptionType::USER_SEAT->value)) {
            $organization
                ->subscription(SubscriptionType::USER_SEAT->value)
                ->updateQuantity($quantity);
        } else {
            // create new sub
            return $organization->newSubscription(
                SubscriptionType::USER_SEAT->value,
                config('subscriptions.user_seats.monthly_price_id')
            )
                ->trialDays(7)
                ->quantity($quantity, config('subscriptions.user_seats.monthly_price_id'))
                ->checkout();
        }
    }

    public function asController(ActionRequest $request)
    {
        $this->handle($request->validated('quantity'));
        
        return redirect()->back()->with('success', 'Subscription updated successfully');
    }

    // public function htmlResponse(Shipment $shipment)
    // {
    //     return redirect()->route('shipments.show', $shipment);
    // }

    // public function jsonResponse(Shipment $shipment)
    // {
    //     return response()->json(ShipmentResource::make($shipment));
    // }

    public function rules()
    {
        return [
            'quantity' => ['required', 'integer', 'min:1']
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(Permission::ORGANIZATION_BILLING);
    }
}
