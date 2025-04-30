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
            $model = $this->resource;
            return [
                'name' => $model->name,
                'color' => $model->color,
                'icon' => $model->icon,
                'label' => $model->label,
            ];
        }

        if ($this->resource instanceof \App\Enums\Shipments\ShipmentFlagType) {
            $enum = $this->resource;
            return [
                'name' => $enum->value,
                'color' => $enum->getColor(),
                'icon' => $enum->getIcon(),
                'label' => $enum->getLabel(),
            ];
        }

        return [];
    }
}
