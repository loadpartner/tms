<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory, HasOrganization;

    protected $fillable = [
        'organization_id',
        'name',
        'address_line_1',
        'address_line_2',
        'address_city',
        'address_state',
        'address_zipcode',
    ];
}
