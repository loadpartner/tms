<?php

namespace App\Http\Resources\Accounting;

use App\Facades\AliasResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Accounting\Receivable
 */
class ReceivableResource extends JsonResource
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
            'shipment_id' => $this->shipment_id,
            'payer_id' => $this->payer_id,
            'payer_type' => AliasResolver::getModelAlias($this->payer_type),
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'rate_type_id' => $this->rate_type_id,
            'currency_code' => $this->currency_code,
            'payer' => $this->payer?->toResource(),
            'rate_type' => $this->rate_type?->toResource(),
        ];
    }
}
