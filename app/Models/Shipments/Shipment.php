<?php

namespace App\Models\Shipments;

use App\Models\Shipper;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'carrier_id',
    ];  

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Shipper, $this>
     */
    public function shippers(): BelongsToMany
    {
        return $this->belongsToMany(Shipper::class, 'shipment_shippers')->using(ShipmentShipper::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentStop, $this>
     */
    public function stops(): HasMany
    {
        return $this->hasMany(ShipmentStop::class);
    }
}
