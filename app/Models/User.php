<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Organizations\Organization;
use App\Models\Organizations\OrganizationInvite;
use App\Models\Organizations\OrganizationUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'current_organization_id',
        'profile_photo_path',
        'timezone',
        'language_preference',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the URL for the user's profile photo.
     *
     * @return string|null
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path) {
            // Extract filename from path
            $filename = basename($this->profile_photo_path);
            return route('profile.photo', ['userId' => $this->id, 'filename' => $filename]);
        }

        return null;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function (User $user) {
            if ($user->isDirty('current_organization_id')) {
                $isMember = $user->organizations()
                    ->where('organizations.id', $user->current_organization_id)
                    ->exists();

                if (!$isMember) {
                    throw new \Illuminate\Auth\Access\AuthorizationException(
                        'You can only set your current organization to one you are a member of.'
                    );
                }
            }
        });
    }

    public function currentOrganization()
    {
        return $this->belongsTo(Organization::class, 'current_organization_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Organization, $this>
     */
    public function organizations() : BelongsToMany
    {
        /** Ignoring due to issue with pivot table returns not being supported by Larastan */
        /** @phpstan-ignore-next-line */
        return $this->belongsToMany(Organization::class, 'organization_users')
            ->using(OrganizationUser::class)
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<OrganizationInvite, $this>
     */
    public function organizationInvites() : HasMany
    {
        return $this->hasMany(OrganizationInvite::class, 'email', 'email');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Timezone, $this>
     */
    public function timezoneDetails() : BelongsTo
    {
        return $this->belongsTo(Timezone::class, 'timezone', 'name');
    }
}
