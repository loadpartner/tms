<?php

namespace App\Models\Accounting;


use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RateType extends Model
{
    use HasOrganization, HasTimestamps, SoftDeletes;

    protected $fillable = [
        'id',
        'organization_id',
        'name',
    ];

    protected $casts = [
        
    ];

}
