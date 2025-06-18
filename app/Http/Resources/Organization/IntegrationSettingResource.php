<?php

namespace App\Http\Resources\Organization;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Organizations\IntegrationSetting
 */
class IntegrationSettingResource extends JsonResource
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
            'key' => $this->key,
            'value' => $this->expose_to_frontend ? $this->value : null,
            'provider' => $this->provider,
            'expose_to_frontend' => $this->expose_to_frontend,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
