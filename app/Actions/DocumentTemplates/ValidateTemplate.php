<?php

namespace App\Actions\DocumentTemplates;

use App\Services\TemplateSecurityService;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ValidateTemplate
{
    use AsAction;

    public function handle(string $template): array
    {
        $templateSecurity = new TemplateSecurityService();
        return $templateSecurity->validateTemplate($template);
    }

    public function rules(): array
    {
        return [
            'template' => ['required', 'string'],
        ];
    }

    public function asController(ActionRequest $request)
    {
        $validated = $request->validated();
        return response()->json($this->handle($validated['template']));
    }
} 