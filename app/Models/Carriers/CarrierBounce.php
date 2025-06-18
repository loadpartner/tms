<?php

namespace App\Models\Carriers;

use App\Models\Carriers\Carrier;
use App\Models\Contact;
use App\Models\Shipments\Shipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class CarrierBounce extends Model
{
    protected $fillable = [
        'carrier_id',
        'shipment_id',
        'driver_id',
        'bounce_cause',
        'reason',
        'bounced_by',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Carrier, $this>
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'driver_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function bouncedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bounced_by', 'id');
    }
}