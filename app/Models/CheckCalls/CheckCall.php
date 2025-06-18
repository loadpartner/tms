<?php

namespace App\Models\CheckCalls;

use App\Enums\ContactMethodType;
use App\Models\Carriers\Carrier;
use App\Models\Note;
use App\Models\Shipments\Shipment;
use App\Models\Shipments\ShipmentStop;
use App\Models\User;
use App\Traits\HasNotes;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class CheckCall extends Model
{
    use HasOrganization, HasNotes, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'carrier_id',
        'shipment_id',
        'user_id',
        'contact_name',
        'contact_method',
        'contact_method_detail',
        'is_late',
        'arrived_at',
        'left_at',
        'eta',
        'is_truck_empty',
        'reported_trailer_temp',
        'loaded_unloaded_at',
        'note_id',
        'next_stop_id',
        'current_stop_id',
    ];

    protected $casts = [
        'details' => 'array',
        'contact_method' => ContactMethodType::class,
        'is_late' => 'boolean',
        'is_truck_empty' => 'boolean',
    ];
    
    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            $model->user_id = $model->user_id ?? Auth::id();
        });

        static::addGlobalScope('order', function ($query) {
            $query->orderBy('created_at', 'desc');
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Carrier, $this>
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Note, $this>
     */
    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ShipmentStop, $this>
     */
    public function nextStop(): BelongsTo
    {
        return $this->belongsTo(ShipmentStop::class, 'next_stop_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<ShipmentStop, $this>
     */
    public function currentStop(): BelongsTo
    {
        return $this->belongsTo(ShipmentStop::class, 'current_stop_id');
    }
} 