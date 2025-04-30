<?php

namespace App\Models\Shipments;

use App\Enums\Shipments\ShipmentFlagType;
use App\Http\Resources\ShipmentFlagTypeResource;
use App\Models\Shipments\Shipment;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShipmentFlag extends Model
{
    use HasOrganization, HasTimestamps, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'type',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the shipment associated with this accessorial type.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipment() : BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function getFlagType() : ShipmentFlagTypeResource
    {
        $enumCheck = ShipmentFlagType::tryFrom($this->type);
        if ($enumCheck) {
            return new ShipmentFlagTypeResource($enumCheck);
        }

        $customType = ShipmentFlagCustomType::where('name', $this->type)->first();
        return new ShipmentFlagTypeResource($customType);

    }



}
