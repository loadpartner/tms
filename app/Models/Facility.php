<?php

namespace App\Models;

use App\Models\Customers\Customer;
use App\Traits\HasAliases;
use App\Traits\HasContacts;
use App\Traits\HasDocuments;
use App\Traits\HasNotes;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Scout\Searchable;

class Facility extends Model
{
    use HasFactory, HasOrganization, Searchable, HasContacts, HasNotes, HasDocuments, HasAliases;

    protected $fillable = [
        'organization_id',
        'location_id',
        'name',
    ];

    protected $appends = [ 'selectable_label' ];

    public $aliasName = 'facility';
    public $aliasProperties = [
        'name' => 'name',
    ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s - %s, %s, %s", $this->name, $this->location->name, $this->location->address_city, $this->location->address_state);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Location, $this>
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Customer, $this>
     */
    public function customers(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        /** Ignoring due to issue with pivot table returns not being supported by Larastan */
        return $this->belongsToMany(Customer::class, 'customer_facilities');
    }
}
