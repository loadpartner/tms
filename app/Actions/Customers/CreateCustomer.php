<?php

namespace App\Actions\Customers;

use App\Http\Resources\Customers\CustomerResource;
use App\Models\Customers\Customer;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateCustomer
{
    use AsAction;

    public function handle(
        string $name,
    ): Customer
    {
        return Customer::create([
            'name' => $name,
        ]);
    }

    public function asController(ActionRequest $request): Customer
    {
        return $this->handle(
            name: $request->validated('name'),
        );
    }

    public function jsonResponse(Customer $customer)
    {
        return CustomerResource::make($customer);
    }

    public function htmlResponse(Customer $customer)
    {
        return redirect()->route('customers.show', $customer);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return $request->user()->can(\App\Enums\Permission::CUSTOMER_EDIT);
    }
}
