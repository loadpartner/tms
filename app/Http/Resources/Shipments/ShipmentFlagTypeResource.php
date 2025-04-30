<?php

namespace App\Http\Resources;

use App\Models\Shipments\ShipmentFlagCustomType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Shipments\ShipmentFlagCustomType
 */
class ShipmentFlagTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        if ($this->resource instanceof ShipmentFlagCustomType) {
            return [
                'name' => $this->name,
                'color' => $this->color,
                'icon' => $this->icon,
                'label' => $this->label,
            ];
        }

        if ($this->resource instanceof \App\Enums\Shipments\ShipmentFlagType) {
            return [
                'name' => $this->value,
                'color' => $this->getColor(),
                'icon' => $this->getIcon(),
                'label' => $this->getLabel(),
            ];
        }

        return [];
    }
}
