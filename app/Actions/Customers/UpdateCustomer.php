<?php

namespace App\Actions\Customers;

use App\Http\Resources\Customers\CustomerResource;
use App\Models\Customers\Customer;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateCustomer
{
    use AsAction;

    public function handle(
        Customer $customer,
        ?string $name = null,
        ?int $net_pay_days = null,
        ?int $billing_location_id = null,
        ?string $dba_name = null,
        ?string $invoice_number_schema = null,
        ?int $billing_contact_id = null,
    ): Customer
    {
        $customer->update([
            'name' => $name,
            'net_pay_days' => $net_pay_days,
            'billing_location_id' => $billing_location_id,
            'dba_name' => $dba_name,
            'invoice_number_schema' => $invoice_number_schema,
            'billing_contact_id' => $billing_contact_id,
        ]);

        return $customer;
    }

    public function asController(ActionRequest $request, Customer $customer): Customer
    {
        return $this->handle(
            customer: $customer,
            name: $request->validated('name'),
            net_pay_days: $request->validated('net_pay_days'),
            billing_location_id: $request->validated('billing_location_id'),
            dba_name: $request->validated('dba_name'),
            invoice_number_schema: $request->validated('invoice_number_schema'),
            billing_contact_id: $request->validated('billing_contact_id'),
        );
    }

    public function jsonResponse(Customer $customer)
    {
        return CustomerResource::make($customer);
    }

    public function htmlResponse(Customer $customer)
    {
        return redirect()->back()->with('success', 'Customer updated successfully');
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'min:3', 'max:255'],
            'net_pay_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'billing_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'dba_name' => ['nullable', 'string', 'max:255'],
            'invoice_number_schema' => ['nullable', 'string', 'max:255'],
            'billing_contact_id' => ['nullable', 'integer', 'exists:contacts,id'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::CUSTOMER_EDIT);
    }
}
