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
            'student_id' => strtoupper(uniqid('STU')),
            'enrollment_date' => $this->faker->date(),
            'user_id' => User::factory(),
        ];
    }
}
