<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'admission_number' => $this->faker->uuid(),
            'enrollment_year' => $this->faker->year(),
            'user_id' => User::factory(),
        ];
    }
}
