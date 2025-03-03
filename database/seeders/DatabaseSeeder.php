<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Enums\Role;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ShieldSeeder::class);

        $admin = User::factory()->create([
            'fullname' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'gender' => Gender::male,
        ]);

        $admin->assignRole(Role::Admin->value);

        $teacher = Teacher::factory()->create([
            'admin_id' => $admin->id,
        ]);

        $teacher->user->assignRole(Role::Teacher->value);

        $student = Student::factory()->create([
            'admin_id' => $admin->id,
        ]);

        $student->user->assignRole(Role::Student->value);
    }
}
