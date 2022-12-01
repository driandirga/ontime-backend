<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->freeEmail(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'gender' => $this->faker->randomElement(['MALE','FEMALE']),
            'age' => $this->faker->numberBetween(17,45),
            'photo' => $this->faker->imageUrl(),
            'team_id' => $this->faker->numberBetween(1,30),
            'role_id' => $this->faker->numberBetween(1,30),
        ];
    }
}
