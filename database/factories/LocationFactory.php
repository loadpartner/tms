<?php

namespace Database\Factories;

use App\Models\Organizations\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->boolean() ? fake()->secondaryAddress() : null,
            'address_city' => fake()->city(),
            'address_state' => fake()->state(),
            'address_zipcode' => fake()->postcode(),
        ];
    }
}

