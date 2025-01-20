<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Organizations\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class FacilityFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['Distribution Center', 'Warehouse', 'Freight Terminal', 'Logistics Hub', 'Cross-dock Facility', 'Storage Facility'];
        $locations = ['North', 'South', 'East', 'West', 'Central', 'Regional', 'Metro'];
        
        return [
            'name' => fake()->randomElement($locations) . ' ' . fake()->randomElement($types) . ' #' . fake()->numberBetween(1, 99),
            'location_id' => Location::factory(),
        ];
    }
}

