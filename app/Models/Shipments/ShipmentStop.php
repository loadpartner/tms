<?php

namespace App\Models\Shipments;

use App\Enums\StopType;
use App\Events\Shipments\ShipmentUpdated;
use App\Models\Facility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasOrganization;
use App\Traits\HasAliases;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class ShipmentStop extends Model implements Auditable
{
    use HasFactory, HasOrganization, HasAliases, AuditableTrait;

    protected $fillable = [
        'organization_id',
        'shipment_id',
        'facility_id',
        'stop_type',
        'special_instructions',
        'reference_numbers',
        'stop_number',
        'eta',
        'arrived_at',
        'loaded_unloaded_at',
        'left_at',
        'appointment_at',
        'appointment_end_at',
        'appointment_type',
    ];

    protected $casts = [
        'stop_type' => StopType::class,
        'eta' => 'datetime',
        'arrived_at' => 'datetime',
        'loaded_unloaded_at' => 'datetime',
        'left_at' => 'datetime',
        'appointment_at' => 'datetime',
        'appointment_end_at' => 'datetime',
    ];

    public $aliasName = 'stop';
    public $aliasProperties = [
        'number' => 'stop_number',
        'type' => 'stop_type',

    ];

    /**
     * Transform audit data to show facility name instead of ID
     */
    public function transformAudit(array $data): array
    {
        // Replace facility_id with facility name in old_values
        if (isset($data['old_values']['facility_id'])) {
            $facilityId = $data['old_values']['facility_id'];
            if ($facilityId) {
                $facility = Facility::find($facilityId);
                if ($facility) {
                    $data['old_values']['facility_id'] = $facility->name;
                }
            }
        }

        // Replace facility_id with facility name in new_values
        if (isset($data['new_values']['facility_id'])) {
            $facilityId = $data['new_values']['facility_id'];
            if ($facilityId) {
                $facility = Facility::find($facilityId);
                if ($facility) {
                    $data['new_values']['facility_id'] = $facility->name;
                }
            }
        }

        return $data;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Default order to the stop number order
        static::addGlobalScope('order', function ($query) {
            $query->orderBy('stop_number', 'asc');
        });

        // When a stop is updated, fire the ShipmentStopsUpdated event
        static::updated(function ($stop) {
            if ($stop->shipment) {
                event(new \App\Events\Shipments\ShipmentStopsUpdated($stop->shipment));

                // Also fire the TMS ShipmentUpdated event
                event(new ShipmentUpdated(
                    shipment: $stop->shipment->fresh(),
                    changedAttributes: ['stops_updated' => true],
                    previousAttributes: [],
                    metadata: [
                        'updated_via' => 'stop_model_update',
                        'stop_id' => $stop->id,
                        'stop_number' => $stop->stop_number,
                    ]
                ));
            }
        });

        // Also fire the event when a stop is created
        static::created(function ($stop) {
            if ($stop->shipment) {
                event(new \App\Events\Shipments\ShipmentStopsUpdated($stop->shipment));

                // Also fire the TMS ShipmentUpdated event
                event(new ShipmentUpdated(
                    shipment: $stop->shipment->fresh(),
                    changedAttributes: ['stop_added' => true],
                    previousAttributes: [],
                    metadata: [
                        'updated_via' => 'stop_model_create',
                        'stop_id' => $stop->id,
                        'stop_number' => $stop->stop_number,
                    ]
                ));
            }
        });

        // And when a stop is deleted
        static::deleted(function ($stop) {
            if ($stop->shipment) {
                event(new \App\Events\Shipments\ShipmentStopsUpdated($stop->shipment));

                // Also fire the TMS ShipmentUpdated event
                event(new ShipmentUpdated(
                    shipment: $stop->shipment->fresh(),
                    changedAttributes: ['stop_deleted' => true],
                    previousAttributes: [],
                    metadata: [
                        'updated_via' => 'stop_model_delete',
                        'stop_id' => $stop->id,
                        'stop_number' => $stop->stop_number,
                    ]
                ));
            }
        });
    }

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

}
