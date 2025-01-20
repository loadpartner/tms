<?php

namespace App\Models\Shipments;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasOrganization;

class ShipmentStopAppointment extends Model
{
    use HasOrganization, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'shipment_stop_id',
        'appointment_datetime',
        'appointment_end_datetime',
        'appointment_type',
    ];
}
