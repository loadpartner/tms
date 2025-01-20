<?php

namespace App\Models\Shipments;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShipmentStop extends Model
{
    use HasFactory, HasOrganization;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'facility_id',
        'stop_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Shipment, $this>
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<ShipmentStopAppointment, $this>
     */
    public function appointment(): HasOne
    {
        return $this->hasOne(ShipmentStopAppointment::class);
    }
}
