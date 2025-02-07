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
    ];

    protected $appends = [ 'selectable_label' ];

    public function getSelectableLabelAttribute() : string
    {
        return sprintf("%s", $this->name);
    }

    public function loads()
    {
        return $this->hasMany(Load::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
