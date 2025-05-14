<?php

namespace App\Models\Customers;

use App\Models\Facility;
use App\Traits\HasAliases;
use App\Traits\HasContacts;
use App\Traits\HasDocuments;
use App\Traits\HasOrganization;
use App\Traits\HasNotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Customer extends Model
{
    use HasFactory, HasOrganization, Searchable, HasNotes, HasContacts, HasDocuments, HasAliases;

    protected $fillable = [
        'organization_id',
        'name',
    ];

    protected $appends = [ 'selectable_label' ];

    public $aliasName = 'customer';
    public $aliasProperties = [
        'name' => 'name',
    ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s", $this->name);
    }

    /**
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Facility, $this>
     */

    public function facilities(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        /** Ignoring due to issue with pivot table returns not being supported by Larastan */
        return $this->belongsToMany(Facility::class, 'customer_facilities');
    }

    public function receivables(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\Accounting\Receivable::class, 'payer');
    }
}
