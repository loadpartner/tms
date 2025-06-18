<?php

namespace App\Http\Resources;

use App\Enums\Documents\DocumentFolder;
use App\Http\Resources\Carriers\CarrierResource;
use App\Http\Resources\Customers\CustomerResource;
use App\Http\Resources\Documents\DocumentFolderResource;
use App\Http\Resources\Documents\DocumentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\Shipment
 */
class ShipmentResource extends JsonResource
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
            'shipment_number' => $this->shipment_number,
            'weight' => $this->weight,
            'trip_distance' => $this->trip_distance,
            'trailer_type' => $this->whenLoaded('trailer_type', TrailerTypeResource::make($this->trailer_type)),
            'trailer_size' => $this->whenLoaded('trailer_size', TrailerSizeResource::make($this->trailer_size)),
            'trailer_type_id' => $this->trailer_type_id,
            'trailer_size_id' => $this->trailer_size_id,
            'trailer_temperature_range' => $this->trailer_temperature_range,
            'trailer_temperature' => $this->trailer_temperature,
            'trailer_temperature_maximum' => $this->trailer_temperature_maximum,
            'driver_id' => $this->driver_id,
            'carrier_id' => $this->carrier_id,
            'customers' => $this->whenLoaded('customers', CustomerResource::collection($this->customers)),
            'carrier' => $this->whenLoaded('carrier', CarrierResource::make($this->carrier)),
            'driver' => $this->whenLoaded('driver', ContactResource::make($this->driver)),
            'stops' => $this->whenLoaded('stops', fn() => ShipmentStopResource::collection($this->stops)),
            'lane' => $this->whenLoaded('stops', fn() => $this->lane()),
            'next_stop' => $this->whenLoaded('stops', fn() => ShipmentStopResource::make($this->nextStop?->load('facility.location'))),
            'current_stop' => $this->whenLoaded('stops', fn() => ShipmentStopResource::make($this->currentStop?->load('facility.location'))),
            'state_label' => $this->state->label(),
            'state' => $this->state,
            
            'documents' => $this->whenLoaded('documents', DocumentResource::collection($this->documents)),
            'document_folders' => $this->whenLoaded('documents', DocumentFolderResource::collection($this->getAllDocumentFolders())),

            'latest_rate_confirmation' => DocumentResource::make(
                $this->documents()->where('folder_name', DocumentFolder::RATECONS)->latest()->first()
            )
        ];
    }
}
