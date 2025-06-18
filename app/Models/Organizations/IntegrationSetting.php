<?php

namespace App\Models\Organizations;

use App\Traits\HasOrganization;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class IntegrationSetting extends Model
{
    use HasOrganization;

    protected $fillable = [
        'organization_id',
        'key',
        'value',
        'provider',
        'expose_to_frontend',
    ];

    protected $casts = [
        'expose_to_frontend' => 'boolean',
    ];

    public function value(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    /**
     * Get the organization that owns this setting
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
} 