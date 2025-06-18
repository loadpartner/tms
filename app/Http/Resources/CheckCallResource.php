<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\CheckCalls\CheckCall
 */
class CheckCallResource extends JsonResource
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
            'organization_id' => $this->organization_id,
            'carrier_id' => $this->carrier_id,
            'shipment_id' => $this->shipment_id,
            'user_id' => $this->user_id,
            'contact_name' => $this->contact_name,
            'contact_method' => $this->contact_method,
            'contact_method_detail' => $this->contact_method_detail,
            'is_late' => $this->is_late,
            'arrived_at' => $this->arrived_at,
            'left_at' => $this->left_at,
            'eta' => $this->eta,
            'is_truck_empty' => $this->is_truck_empty,
            'reported_trailer_temp' => $this->reported_trailer_temp,
            'loaded_unloaded_at' => $this->loaded_unloaded_at,
            'note_id' => $this->note_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'next_stop_id' => $this->next_stop_id,
            'current_stop_id' => $this->current_stop_id,
            'carrier' => $this->whenLoaded('carrier'),
            'shipment' => $this->whenLoaded('shipment'),
            'creator' => $this->whenLoaded('creator', function () {
                return UserResource::make($this->creator);
            }),
            'note' => $this->whenLoaded('note', function () {
                return NoteResource::make($this->note);
            }),
            'next_stop' => $this->whenLoaded('nextStop', function () {
                return ShipmentStopResource::make($this->nextStop);
            }),
            'current_stop' => $this->whenLoaded('currentStop', function () {
                return ShipmentStopResource::make($this->currentStop);
            }),
        ];
    }
} 