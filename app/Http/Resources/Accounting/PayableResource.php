<?php

namespace App\Http\Resources\Accounting;

use App\Facades\AliasResolver;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @mixin \App\Models\Accounting\Payable
 */
class PayableResource extends JsonResource
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
            'payee_id' => $this->payee_id,
            'payee_type' => AliasResolver::getModelAlias($this->payee_type),
            'rate' => $this->rate,
            'quantity' => $this->quantity,
            'total' => $this->total,
            'rate_type_id' => $this->rate_type_id,
            'currency_code' => $this->currency_code,
            'payee' => $this->payee?->toResource(),
            'rate_type' => $this->rate_type?->toResource(),
        ];
    }
}
