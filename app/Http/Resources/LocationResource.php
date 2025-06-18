<?php

namespace App\Http\Resources;

use App\Actions\ZipToTimezone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Location
 */
class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $timezones = ZipToTimezone::run($this->address_zipcode);

        $timezone = null;
        if ($timezones) {
            $timezone = $timezones[$this->address_zipcode];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'address_city' => $this->address_city,
            'address_state' => $this->address_state,
            'address_zipcode' => $this->address_zipcode,
            'selectable_label' => $this->selectable_label,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'timezone' => $timezone,
        ];
    }
}

