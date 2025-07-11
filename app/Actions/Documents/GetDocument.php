<?php

namespace App\Actions\Documents;

use App\Http\Resources\Documents\DocumentResource;
use App\Models\Documents\Document;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GetDocument
{
    use AsAction;

    public function handle(
        Document $document
    )
    {
        return $document;
    }

    public function htmlResponse(Document $document)
    {
        // if using local storage, return file with filename
        if (Storage::getDefaultDriver() === 'local') {
            return Storage::download($document->path, $document->name);
        }
        return redirect($document->getTemporaryUrl());
    }

    public function jsonResponse(Document $document)
    {
        return response()->json(DocumentResource::make($document));
    }

    public function asController(ActionRequest $request, Document $document)
    {

        return $this->handle(
            document: $document,
        );
    }

    public function authorize(ActionRequest $request) 
    {
        $document = $request->route('document');
        return $request->user()->can('view', $document);
    }

    public function rules()
    {
        return [];
    }
}