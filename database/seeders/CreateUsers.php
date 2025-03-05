<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateUsers extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            ['fullname' => 'Admin User', 'email' => 'admin@demo.com', 'password' => bcrypt('password')],
            ['fullname' => 'Teacher User', 'email' => 'teacher@demo.com', 'password' => bcrypt('password')],
            ['fullname' => 'Student User', 'email' => 'student@demo.com', 'password' => bcrypt('password')],
        ]);

        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');
        $teacherRoleId = DB::table('roles')->where('name', 'Teacher')->value('id');
        $studentRoleId = DB::table('roles')->where('name', 'Student')->value('id');

        $adminUserId = DB::table('users')->where('email', 'admin@demo.com')->value('id');
        $teacherUserId = DB::table('users')->where('email', 'teacher@demo.com')->value('id');
        $studentUserId = DB::table('users')->where('email', 'student@demo.com')->value('id');

        DB::table('model_has_roles')->insert([
            ['role_id' => $adminRoleId, 'model_type' => 'App\Models\User', 'model_id' => $adminUserId],
            ['role_id' => $teacherRoleId, 'model_type' => 'App\Models\User', 'model_id' => $teacherUserId],
            ['role_id' => $studentRoleId, 'model_type' => 'App\Models\User', 'model_id' => $studentUserId],
        ]);

        DB::table('teachers')->insert([
            ['employee_number' => strtoupper(uniqid('EMP')), 'user_id' => $teacherUserId, 'admin_id' => $adminUserId],
        ]);

        DB::table('students')->insert([
            ['student_id' => strtoupper(uniqid('STU')), 'enrollment_date' => now(), 'user_id' => $studentUserId, 'admin_id' => $adminUserId],
        ]);
    }
}
