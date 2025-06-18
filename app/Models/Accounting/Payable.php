<?php

namespace App\Models\Accounting;

use App\Models\Shipments\Shipment;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payable extends Model
{
    use HasOrganization, HasTimestamps, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'payee_id',
        'payee_type',
        'rate',
        'quantity',
        'total',
        'rate_type_id',
        'currency_code',
    ];

    public function shipment() : BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function payee() : MorphTo
    {
        return $this->morphTo();
    }

    public function rate_type() : BelongsTo
    {
        return $this->belongsTo(RateType::class);
    }
}