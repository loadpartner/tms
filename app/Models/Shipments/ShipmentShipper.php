<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasOrganization;

class ShipmentShipper extends Pivot
{
    use HasOrganization;

    protected $table = 'shipment_shippers';

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'shipper_id',
    ];
}
