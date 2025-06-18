<?php

namespace App\Actions\DocumentTemplates;

use App\Models\Documents\DocumentTemplate;
use App\Models\Organizations\Organization;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDocumentTemplates
{
    use AsAction;

    public function handle(Organization $organization)
    {
        return $organization->documentTemplates()
            ->select(['id', 'template_type', 'created_at', 'updated_at'])
            ->get();
    }

    public function asController(ActionRequest $request, Organization $organization)
    {
        return $this->handle($organization);
    }
} 