<?php

namespace App\Models;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    use HasFactory, HasOrganization;

    protected $fillable = [
        'organization_id',
        'name',
    ];
}
