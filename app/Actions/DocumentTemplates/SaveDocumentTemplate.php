<?php

namespace App\Actions\DocumentTemplates;

use App\Enums\Documents\DocumentTemplateType;
use App\Models\Documents\DocumentTemplate;
use App\Models\Organizations\Organization;
use App\Services\TemplateSecurityService;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SaveDocumentTemplate
{
    use AsAction;

    public function rules(): array
    {
        return [
            'template_type' => ['required', 'string', 'in:carrier_rate_confirmation,customer_invoice,bill_of_lading'],
            'template' => ['required', 'string', 'min:1'],
        ];
    }

    public function handle(Organization $organization, array $data)
    {
        $templateSecurity = new TemplateSecurityService();
        
        // Sanitize the template for security
        $sanitizedTemplate = $templateSecurity->sanitizeTemplate($data['template']);
        
        return DocumentTemplate::updateOrCreate(
            [
                'organization_id' => $organization->id,
                'template_type' => DocumentTemplateType::from($data['template_type']),
            ],
            [
                'template' => $sanitizedTemplate,
            ]
        );
    }

    public function asController(ActionRequest $request, Organization $organization)
    {
        $validated = $request->validated();
        
        $this->handle($organization, $validated);

        return redirect()->back()->with('success', 'Document template saved successfully.');
    }
} 