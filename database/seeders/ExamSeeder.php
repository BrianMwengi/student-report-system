<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all subjects ids (no need to get all columns)
        $subjectIds = Subject::all('id')->pluck('id')->toArray();

        $faker = Faker::create();

        // Process students in chunks
        Student::chunk(200, function ($students) use ($subjectIds, $faker) {
            foreach ($students as $student) {
                foreach ($subjectIds as $subjectId) {
                    $exam = Exam::create([
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'exam1' => rand(0, 30),  // Replace with your logic
                        'exam2' => rand(0, 30),  // Replace with your logic
                        'exam3' => rand(0, 70),  // Replace with your logic
                        'teacher' => $faker->name, 
                    ]);
            
                }
            }
        });
    }
}
