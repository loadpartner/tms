<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Shipper
 */
class ShipperResource extends JsonResource
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
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'contact_name' => $this->contact_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'selectable_label' => $this->selectable_label,
            'locations' => LocationResource::collection($this->whenLoaded('locations')),
            'notes' => NoteResource::collection($this->whenLoaded('notes')),
            'shipments' => ShipmentResource::collection($this->whenLoaded('shipments')),
        ];
    }
}
