<?php

namespace App\Http\Resources;

use App\Facades\AliasResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * This resource is used to return a model that HasAliases so it can be plugged
 * into a select on the FE with other model types but still be differentiated
 * 
 * @mixin \App\Models\Carriers\Carrier
 */
class AliasModelResource extends JsonResource
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
            'alias_name' => AliasResolver::getModelAlias(get_class($this->resource)),
            'label' => $this->selectable_label,
        ];
    }
}

