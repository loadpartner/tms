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
            'mc_number' => $this->mc_number,
            'dot_number' => $this->dot_number,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'locations' => $this->whenLoaded('locations'),
            'contacts' => $this->whenLoaded('contacts'),
            'notes' => $this->whenLoaded('notes'),
            'documents' => $this->whenLoaded('documents'),
        ];
    }
}
