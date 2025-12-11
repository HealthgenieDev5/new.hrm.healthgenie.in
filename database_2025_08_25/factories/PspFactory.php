<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Psp>
 */
class PspFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'logo' => $this->faker->imageUrl(100, 100, 'business', true, 'Logo'), // Fake logo URL
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'state' => $this->faker->state,
            'pincode' => $this->faker->postcode,
            'mobile_number' => $this->faker->unique()->numerify('##########'), // 10-digit mobile number
            'alternate_number' => $this->faker->numerify('##########'), // Another 10-digit mobile number
            'email_id' => $this->faker->unique()->safeEmail,
            'contact_person' => $this->faker->name,
        ];
    }
}
