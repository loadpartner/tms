<?php

namespace App\Actions\Organizations;

use App\Models\Organizations\Organization;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateOrganization
{
    use AsAction;

    public function handle(
        Organization $organization,
        string $name,
        ?string $company_name = null,
        ?int $company_location_id = null,
        ?string $company_phone = null,
        ?string $company_email = null,
        ?string $accounting_contact_email = null,
        ?string $accounting_contact_phone = null,
    ): Organization {
        $organization->update([
            'name' => $name,
            'company_name' => $company_name,
            'company_location_id' => $company_location_id,
            'company_phone' => $company_phone,
            'company_email' => $company_email,
            'accounting_contact_email' => $accounting_contact_email,
            'accounting_contact_phone' => $accounting_contact_phone,
        ]);

        return $organization->fresh();
    }

    public function asController(ActionRequest $request, Organization $organization)
    {
        $validated = $request->validated();
        
        $this->handle(
            $organization,
            $validated['name'],
            $validated['company_name'] ?? null,
            $validated['company_location_id'] ?? null,
            $validated['company_phone'] ?? null,
            $validated['company_email'] ?? null,
            $validated['accounting_contact_email'] ?? null,
            $validated['accounting_contact_phone'] ?? null,
        );

        return redirect()->back()->with('success', 'Organization settings updated successfully');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'company_phone' => ['nullable', 'string', 'max:20'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'accounting_contact_email' => ['nullable', 'email', 'max:255'],
            'accounting_contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function authorize(ActionRequest $request): bool
    {
        return true;
    }
}
