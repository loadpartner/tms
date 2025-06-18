<?php

namespace App\Http\Resources\Customers;

use App\Http\Resources\ContactResource;
use App\Http\Resources\Documents\DocumentFolderResource;
use App\Http\Resources\Documents\DocumentResource;
use App\Http\Resources\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Customers\Customer
 */
class CustomerResource extends JsonResource
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
            'net_pay_days' => $this->net_pay_days,
            'billing_location' => LocationResource::make($this->billingLocation),
            'dba_name' => $this->dba_name,
            'invoice_number_schema' => $this->invoice_number_schema,
            'billing_contact' => ContactResource::make($this->billingContact),

            'documents' => $this->whenLoaded('documents', DocumentResource::collection($this->documents)),
            'document_folders' => $this->whenLoaded('documents', DocumentFolderResource::collection($this->getAllDocumentFolders()))
        ];
    }
}
