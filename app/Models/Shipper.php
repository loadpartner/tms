<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Shipper extends Model
{
    use HasFactory, HasOrganization, Searchable;

    protected $fillable = [
        'organization_id',
        'name',
        'mc_number',
        'dot_number',
        'status',
    ];

    protected $appends = [ 'selectable_label' ];

    protected $casts = [
        'status' => 'string',
    ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s", $this->name);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }
}
