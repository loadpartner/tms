<?php

namespace App\Actions\Shipments;

use App\Events\Customers\CustomerAssigned;
use App\Events\Customers\CustomerUnassigned;
use App\Models\Customers\Customer;
use App\Models\Shipments\Shipment;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateShipmentCustomers
{
    use AsAction;

    public function handle(
        Shipment $shipment,
        array $customerIds,
    ): Shipment {
        $previousCustomerIds = $shipment->customers()->pluck('customers.id')->toArray();

        // Sync with organization_id for the pivot table
        $syncData = [];
        foreach ($customerIds as $customerId) {
            $syncData[$customerId] = ['organization_id' => $shipment->organization_id];
        }

        $shipment->customers()->sync($syncData);

        $addedCustomerIds = array_diff($customerIds, $previousCustomerIds);
        $removedCustomerIds = array_diff($previousCustomerIds, $customerIds);

        // Dispatch customer change events
        if (!empty($addedCustomerIds) || !empty($removedCustomerIds)) {
            $this->dispatchCustomerChangeEvents($shipment, $addedCustomerIds, $removedCustomerIds);

            // Touch the model to trigger updated event
            $shipment->touch();
        }

        return $shipment;
    }

    private function dispatchCustomerChangeEvents(
        Shipment $shipment,
        array $addedCustomerIds,
        array $removedCustomerIds
    ): void {
        $allCustomerIds = array_merge($addedCustomerIds, $removedCustomerIds);

        if (empty($allCustomerIds)) {
            return;
        }

        /** @var Collection<int, Customer> $customers */
        $customers = Customer::query()
            ->whereIn('id', $allCustomerIds)
            ->get()
            ->keyBy('id');

        // Dispatch events for removed customers
        foreach ($removedCustomerIds as $customerId) {
            $customer = $customers->get($customerId);
            if ($customer) {
                event(new CustomerUnassigned($shipment, $customer, [
                    'unassigned_via' => 'shipment_customer_update',
                ]));
            }
        }

        // Dispatch events for added customers
        foreach ($addedCustomerIds as $customerId) {
            $customer = $customers->get($customerId);
            if ($customer) {
                event(new CustomerAssigned($shipment, $customer, [
                    'assigned_via' => 'shipment_customer_update',
                ]));
            }
        }
    }

    public function asController(ActionRequest $request, Shipment $shipment)
    {
        $this->handle(
            $shipment,
            $request->customer_ids,
        );

        return redirect()->back()->with('success', 'Shipment customers updated successfully');
    }

    public function rules(): array
    {
        return [
            'customer_ids' => ['required', 'array'],
            'customer_ids.*' => ['required', 'exists:customers,id'],
        ];
    }   

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::SHIPMENT_EDIT);
    }
}
