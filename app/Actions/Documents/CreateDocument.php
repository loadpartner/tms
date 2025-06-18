<?php

namespace App\Actions\Documents;

use App\Enums\Documents\Documentable;
use App\Http\Resources\Documents\DocumentResource;
use App\Models\Documents\Document;
use Exception;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateDocument
{
    use AsAction;

    public function handle(
        string $documentableType,
        int $documentableId,
        string $fileName,
        File|UploadedFile $file,
        ?string $folderName = null,
    )
    {

        $documentable = Documentable::from($documentableType)->getClassName()::findOrFail($documentableId);

        // Generate a unique filename for filesystem storage to prevent collisions
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $uniqueFileName = Str::uuid() . '.' . $extension;

        // Store the file with the unique filename
        $path = Storage::putFileAs(
            sprintf('documents/%s/%s', $documentableType, $documentable->id),
            $file,
            $uniqueFileName,
        );

        // Save the document with the original filename for display purposes
        $document = Document::create([
            'name' => $fileName, // Original filename for display
            'folder_name' => $folderName,
            'documentable_id' => $documentable->id,
            'documentable_type' => $documentable->getMorphClass(),
            'path' => $path, // Path includes the unique filename
            'uploaded_by' => Auth::user()?->id,
        ]);

        return $document;
    }

    public function htmlResponse(Document $document)
    {
        return redirect()->back()->with('success', 'Document created successfully');
    }

    public function jsonResponse(Document $document)
    {
        return response()->json(DocumentResource::make($document));
    }

    public function asController(ActionRequest $request)
    {

        return $this->handle(
            documentableType: $request->input('documentable_type'),
            documentableId: $request->input('documentable_id'),
            fileName: $request->file('file')->getClientOriginalName(),
            folderName: $request->input('folder_name'),
            file: $request->file('file'),
        );
    }

    public function authorize(ActionRequest $request) 
    {
        $documentableType = $request->input('documentable_type');
        $documentableId = $request->input('documentable_id');

        $documentable = Documentable::from($documentableType)
            ->getClassName()
            ::findOrFail($documentableId);

        return $request->user()->can('create', [Document::class, $documentable]);
    }

    public function rules()
    {
        return [
            'file' => 'required|file',
            'folder_name' => 'nullable|string|max:255',
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|string'
        ];
    }
}