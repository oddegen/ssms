<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\Role;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ShieldSeeder::class);

        $admin = User::factory()->create([
            'fullname' => 'Admin',
            'email' => 'admin@demo.com',
            'password' => bcrypt('password'),
            'gender' => Gender::male,
        ]);
        $admin->assignRole(Role::Admin->value);

        $teacher = Teacher::factory()
            ->for(User::factory()->state([
                'email' => 'teacher@demo.com',
                'password' => bcrypt('password'),
            ]))
            ->create([
                'admin_id' => $admin->id,
            ]);
        $teacher->user->assignRole(Role::Teacher->value);

        $this->call(SubjectSeeder::class);
        $this->call(GradeSeeder::class);

        $subjects = Subject::query()->pluck('id')->random(4);
        $grade = Grade::query()->pluck('id')->random();

        $teacher->subjects()->attach($subjects, ['grade_id' => $grade]);

        $student = Student::factory()
            ->for(User::factory()->state([
                'email' => 'student@demo.com',
                'password' => bcrypt('password'),
            ]))
            ->create([
                'admin_id' => $admin->id,
                'grade_id' => $grade,
            ]);
        $student->user->assignRole(Role::Student->value);
    }
}
