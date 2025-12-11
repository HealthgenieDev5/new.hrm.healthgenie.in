<?php

namespace Database\Factories;

use App\Models\Psp;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Worker>
 */
class WorkerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)), // Random alphanumeric code
            'name' => $this->faker->name, // Worker name
            'father_name' => $this->faker->name, // Father's name
            'date_of_birth' => $this->faker->date('Y-m-d', '2002-12-31'), // Random DOB before 2002
            'date_of_joining' => $this->faker->date('Y-m-d'), // Random Date of Joining
            'employment_status' => $this->faker->randomElement(['Active', 'Inactive', 'Terminated', 'Absconded', 'Retired']), // Random Employment Status
            'mobile_number' => $this->faker->numerify('##########'), // 10-digit random mobile number
            'esi_number' => strtoupper(Str::random(10)), // Random ESI Number
            'gender' => $this->faker->randomElement(['Male', 'Female', 'Other']), // Random Gender
            'marital_status' => $this->faker->randomElement(['Unmarried', 'Married', 'Divorced', 'Widowed']), // Random Marital Status
            'adhar_number' => $this->faker->numerify('############'), // Random 12-digit Aadhaar Number
            'permanent_address' => $this->faker->address, // Random Address
            'present_address' => $this->faker->address, // Random Address
            'photo' => $this->faker->imageUrl(100, 100, 'people'), // Random photo URL
            'psp_id' => Psp::factory(),
        ];
    }
}
