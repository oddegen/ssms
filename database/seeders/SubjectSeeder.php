<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = collect([
            ['name' => 'Mathematics', 'code' => 'MATH'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'History', 'code' => 'HIS'],
            ['name' => 'Geography', 'code' => 'GEO'],
            ['name' => 'Computer Science', 'code' => 'CS'],
            ['name' => 'Physical Education', 'code' => 'PE'],
            ['name' => 'Music', 'code' => 'MUS'],
            ['name' => 'Art', 'code' => 'ART'],
            ['name' => 'Drama', 'code' => 'DRA'],
        ]);

        $subjects->each(fn ($subject) => Subject::create($subject));
    }
}
