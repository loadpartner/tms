<?php

namespace App\Models\Organizations;

use App\Models\User;
use App\Models\Location;
use App\Models\Documents\DocumentTemplate;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Cashier\Billable;

class Organization extends Model
{
    use HasTimestamps, HasFactory, Billable;

    protected $fillable = [
        'id',
        'name',
        'owner_id',
        'company_name',
        'company_location_id',
        'company_phone',
        'company_email',
        'accounting_contact_email',
        'accounting_contact_phone',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Organization $organization) {
            // Set the current organization context for the defaults creation
            \App\Actions\Defaults\CreateOrUpdateOrganizationDefaults::run($organization->id);
        });
    }

    public function owner() : BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the company location for the organization.
     */
    public function companyLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'company_location_id', 'id');
    }

    public function users() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_users')
            ->using(OrganizationUser::class)
            ->withTimestamps();
    }

    public function invites() : HasMany
    {
        return $this->hasMany(OrganizationInvite::class);
    }

    public function roles() : HasMany
    {
        return $this->hasMany(
                config('permission.models.role')
        );
    }
    
    /**
     * Get the integration settings for this organization
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<IntegrationSetting, $this>
     */
    public function integration_settings() : HasMany
    {
        return $this->hasMany(IntegrationSetting::class);
    }

    /**
     * Get the document templates for this organization
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<DocumentTemplate, $this>
     */
    public function documentTemplates() : HasMany
    {
        return $this->hasMany(DocumentTemplate::class);
    }
}
