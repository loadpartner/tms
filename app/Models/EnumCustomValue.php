<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Organizations\Organization;
use App\Traits\HasOrganization;

class EnumCustomValue extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'enum_class',
        'value',
        'label',
    ];

    /**
     * Get the organization that owns the enum custom value.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
} 