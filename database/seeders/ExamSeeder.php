<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

use App\Jobs\UpdateExamScoresJob;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all subjects ids (no need to get all columns)
        $subjectIds = Subject::all('id')->pluck('id')->toArray();

        $faker = Faker::create();
        
        Student::chunk(200, function ($students) use ($subjectIds, $faker) {
            foreach ($students as $student) {
                foreach ($subjectIds as $subjectId) {
                    try {
                        // Generate a full name
                        $fullName = $faker->name;
                        // Convert the full name to initials
                        $initials = $this->getInitials($fullName);

                        $exam = Exam::create([
                            'student_id' => $student->id,
                            'subject_id' => $subjectId,
                            'exam1' => rand(0, 30),  // Replace with your logic
                            'exam2' => rand(0, 30),  // Replace with your logic
                            'exam3' => rand(0, 70),  // Replace with your logic
                            'teacher' => $initials, 
                        ]);

                        UpdateExamScoresJob::dispatch($exam);
                        
                    } catch (\Exception $e) {
                        Log::error('Error creating exam record: ' . $e->getMessage());
                    }
                }
            }
        });
    }

    /**
     * Helper function to convert a full name to initials.
     */
    private function getInitials($name)
    {
        $parts = explode(' ', $name);
        $initials = '';

        foreach ($parts as $part) {
            $initials .= strtoupper($part[0]);
        }

        return $initials;
    }
}

