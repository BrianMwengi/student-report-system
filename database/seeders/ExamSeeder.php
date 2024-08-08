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
        
        // Process students in chunks of 200 to avoid memory overload
        Student::chunk(200, function ($students) use ($subjectIds, $faker) {
            // Iterate through each student in the current chunk
            foreach ($students as $student) {
                // Iterate through each subject ID for the current student
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
        // Split the name into parts using space as the delimiter
        $parts = explode(' ', $name);
        
        // Initialize an empty string to hold the initials
        $initials = '';
    
        // Loop through each part of the name
        foreach ($parts as $part) {
            // Append the uppercase first letter of each part to the initials
            $initials .= strtoupper($part[0]);
        }
    
        // Return the concatenated initials
        return $initials;
    }
}

