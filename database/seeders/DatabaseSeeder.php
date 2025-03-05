<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ShieldSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(GradeSeeder::class);
        $this->call(CreateUsers::class);
    }
}
