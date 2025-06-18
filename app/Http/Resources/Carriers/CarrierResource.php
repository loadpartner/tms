<?php

namespace App\Http\Resources\Carriers;

use App\Http\Resources\LocationResource;
use App\Http\Resources\Carriers\CarrierSaferReportResource;
use App\Http\Resources\ContactResource;
use App\Http\Resources\Documents\DocumentFolderResource;
use App\Http\Resources\Documents\DocumentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Carriers\Carrier
 */
class CarrierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'mc_number' => $this->mc_number,
            'dot_number' => $this->dot_number,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'physical_location_id' => $this->physical_location_id,
            'billing_email' => $this->billing_email,
            'billing_phone' => $this->billing_phone,
            'billing_location_id' => $this->billing_location_id,

            'physical_location' => $this->whenLoaded('physical_location', function () {
                return new LocationResource($this->physical_location);
            }),
            
            'billing_location' => $this->whenLoaded('billing_location', function () {
                return new LocationResource($this->billing_location);
            }),

            'safer_report' => $this->whenLoaded('safer_report', function () {
                return new CarrierSaferReportResource($this->safer_report);
            }),

            'contacts' => $this->whenLoaded('contacts', ContactResource::collection($this->contacts)),

            'documents' => $this->whenLoaded('documents', DocumentResource::collection($this->documents)),
            'document_folders' => $this->whenLoaded('documents', DocumentFolderResource::collection($this->getAllDocumentFolders()))
        ];
    }
}
