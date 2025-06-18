<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $key
 * @property mixed $value
 * @property string $provider
 * @property bool $expose_to_frontend
 * @property string $label
 * @property string $description
 */
class GlobalIntegrationSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this['key'],
            'value' => $this['expose_to_frontend'] ? $this['value'] : null,
            'provider' => $this['provider'],
            'expose_to_frontend' => $this['expose_to_frontend'],
            'label' => $this['label'],
            'description' => $this['description'],
        ];
    }
}
