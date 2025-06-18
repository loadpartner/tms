<?php

namespace App\Actions\DocumentTemplates;

use App\Models\Documents\DocumentTemplate;
use App\Models\Organizations\Organization;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDocumentTemplate
{
    use AsAction;

    public function handle(Organization $organization, DocumentTemplate $documentTemplate)
    {
        // Ensure the template belongs to the organization
        if ($documentTemplate->organization_id !== $organization->id) {
            abort(404);
        }

        return $documentTemplate;
    }

    public function asController(ActionRequest $request, Organization $organization, DocumentTemplate $documentTemplate)
    {
        return $this->handle($organization, $documentTemplate);
    }
} 