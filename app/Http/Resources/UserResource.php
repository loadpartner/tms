<?php

namespace App\Http\Resources;

use App\Traits\Resources\HasDates;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin \App\Models\User
 */
class UserResource extends JsonResource
{
    use HasDates;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->asDate($this->created_at),
            'updated_at' => $this->asDate($this->updated_at),
            'profile_photo_url' => $this->profile_photo_url,
            'organizations' => $this->whenLoaded('organizations', function () {
                return $this->organizations;
            }),
            'current_organization_id' => $this->current_organization_id,
            'timezone' => $this->timezone,
            'language_preference' => $this->language_preference,
        ];
    }
}
