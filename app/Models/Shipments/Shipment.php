<?php

namespace App\Models\Shipments;

use App\Http\Resources\AliasModelResource;
use App\Http\Resources\ShipmentResource;
use App\Models\Accounting\Payable;
use App\Models\Accounting\Receivable;
use App\Models\Carriers\Carrier;
use App\Models\Carriers\CarrierBounce;
use App\Models\CheckCalls\CheckCall;
use App\Models\Contact;
use App\Models\Customers\Customer;
use App\States\Shipments\ShipmentState;
use App\Traits\HasAliases;
use App\Traits\HasDocuments;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasOrganization;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\ModelStates\HasStates;
use Spatie\ModelStates\HasStatesContract;

class Shipment extends Model implements HasStatesContract
{
    use HasOrganization, Searchable, HasFactory, HasNotes, HasStates, HasDocuments, HasAliases;

    protected $fillable = [
        'organization_id',
        'carrier_id',
        'driver_id',
        'weight',
        'trip_distance',
        'trailer_type_id',
        'trailer_size_id',
        'trailer_temperature_range',
        'trailer_temperature',
        'trailer_temperature_maximum',
        'shipment_number',
        'state',
    ];

    protected $casts = [
        'trailer_temperature_range' => 'boolean',
        'state' => ShipmentState::class,
    ];

    protected $appends = ['selectable_label'];

    public $aliasName = 'shipment';
    public $aliasProperties = [
        'number' => 'shipment_number',
        'lane' => 'function:lane',
        'driver' => 'driver',
    ];

    public function getSelectableLabelAttribute(): string
    {
        return sprintf("Shipment %s", $this->id);
    }

    /**
     * Defines the searchable content for scout search
     */
    public function toSearchableArray()
    {
        return new ShipmentResource(
            $this->load('carrier', 'customers', 'stops', 'trailer_type', 'trailer_size')
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TrailerType, $this>
     */
    public function trailer_type(): BelongsTo
    {
        return $this->belongsTo(TrailerType::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<TrailerSize, $this>
     */
    public function trailer_size(): BelongsTo
    {
        return $this->belongsTo(TrailerSize::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Carrier, $this>
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Contact, $this>
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'driver_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CarrierBounce, $this>
     */
    public function bounces() : HasMany
    {
        return $this->hasMany(CarrierBounce::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Customer, $this>
     */
    public function customers(): BelongsToMany
    {
        /** Ignoring due to issue with pivot table returns not being supported by Larastan */
        /** @phpstan-ignore-next-line */
        return $this->belongsToMany(Customer::class, 'shipment_customers')->using(ShipmentCustomer::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ShipmentStop, $this>
     */
    public function stops(): HasMany
    {
        return $this->hasMany(ShipmentStop::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Payable, $this>
     */
    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Receivable, $this>
     */
    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<CheckCall, $this>
     */
    public function check_calls(): HasMany
    {
        return $this->hasMany(CheckCall::class);
    }

    public function getRelatedEntitiesAttribute(): array
    {
        $entities = [];

        foreach ($this->customers as $customer) {
            $entities[] = $customer;
        }

        if($this->carrier) {
            $entities[] = $this->carrier;
        }
        
        foreach($this->stops as $stop) {
            $entities[] = $stop->facility;
        }

        foreach($this->bounces as $bounce) {
            $entities[] = $bounce->carrier;
        }

        return $entities;
    }

    public function getNextStopAttribute(): ?ShipmentStop
    {
        return $this->stops()->whereNull('arrived_at')->first();
    }

    public function getCurrentStopAttribute(): ?ShipmentStop
    {
        return $this->stops()->whereNotNull('arrived_at')->whereNull('left_at')->first();
    }

    public function getPreviousStopAttribute(): ?ShipmentStop
    {
        return $this->stops()->whereNotNull('arrived_at')->latest()->first();
    }

    public function lane(): string
    {
        return sprintf(
            "%s - %s",
            $this->stops()->first()?->facility->location->state_shorthand,
            $this->stops()->latest('stop_number')->first()?->facility->location->state_shorthand
        );
    }
}
