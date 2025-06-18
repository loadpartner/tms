<?php

namespace App\Models;

use App\Http\Resources\ContactResource;
use App\Traits\HasAliases;
use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Scout\Searchable;

class Contact extends Model
{
    use HasOrganization, Searchable, HasAliases;

    protected $fillable = [
        'organization_id',
        'contact_type',
        'title',
        'name',
        'email',
        'mobile_phone',
        'office_phone',
        'office_phone_extension',
        'contact_for_id',
        'contact_for_type',
    ];

    protected $appends = [ 'selectable_label' ];

    public $aliasName = 'contact';
    public $aliasProperties = [
        'name' => 'name',
        'email' => 'email',
        'mobile_phone' => 'mobile_phone',
        'office_phone' => 'office_phone',
        'office_phone_extension' => 'office_phone_extension',
    ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf('%s', $this->name);
    }

    /**
     * Defines the searchable content for scout search
     */
    public function toSearchableArray()
    {
        return new ContactResource($this);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<Model, $this>
     */
    public function contactFor() : MorphTo
    {
        return $this->morphTo();
    }
}
