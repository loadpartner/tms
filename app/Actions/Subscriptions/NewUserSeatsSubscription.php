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

class NewUserSeatsSubscription
{
    use AsAction;

    public function handle(
        int $quantity = 1
    ) {
        // Check if billing is enabled
        if (!config('subscriptions.enable_billing')) {
            abort(403, 'Billing is disabled');
        }

        $organization = current_organization();

        return $organization
            ->newSubscription(
                SubscriptionType::USER_SEAT->value,
                config('subscriptions.user_seats.monthly_price_id')
            )
            ->quantity($quantity, config('subscriptions.user_seats.monthly_price_id'))
            ->trialDays(7)
            ->allowPromotionCodes()
            ->checkout(
                [
                    'success_url' => route('products-list', ['success' => true]),
                    'cancel_url' => route('products-list', ['cancel' => true]),
                ]
            );
    }

    public function asController(ActionRequest $request)
    {
        return $this->handle(quantity: $request->validated('seat_count'));
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
            'seat_count' => ['required', 'integer', 'min:0']
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return config('subscriptions.enable_billing') && $request->user()->can(Permission::ORGANIZATION_BILLING);
    }
}
