<?php

namespace App\Models\Shipments;

use App\Models\Shipments\Shipment;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentFlagCustomType extends Model
{
    use HasOrganization, HasTimestamps, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'name',
        'color',
        'icon',
        'label',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function shipmentFlags() : HasMany
    {
        return $this->hasMany(ShipmentFlag::class, 'type', 'name');
    }
    
    

}
