<?php

namespace App\Jobs;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExamScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $exam;

    /**
     * Create a new job instance.
     *
     * @param  Exam  $exam
     * @return void
     */
    public function __construct(Exam $exam)
    {
        // Assign the exam to the $exam property
        $this->exam = $exam;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $exam = $this->exam;
        $student = Student::find($exam->student_id);

        // Calculate the average score based on the three exams.
        // First, we average the scores of the two CATs
        $averageCATs = ($exam->exam1 + $exam->exam2) / 2;

        // Then, we convert the average CAT score to a scale of 30 and calculate its contribution to the final score
        $catScore = ($averageCATs / 30) * 30;

        // Next, we convert the final exam score to a scale of 70 and calculate its contribution to the final score
        $finalExamScore = ($exam->exam3 / 70) * 70;

        // Finally, we add these two percentages to get the final score
        $average = $catScore + $finalExamScore;

        // Round the average to the nearest whole number
        $exam->average = round($average);

        $exam->grade = $this->calculateGrade($exam->average);
        $exam->points = $this->calculatePoints($exam->grade);
        $exam->position = $this->calculateSubjectPosition($exam->subject_id, $exam->average, $student->form, $student->stream_id);
        $exam->remarks = $this->generateRemarks($exam->grade);

        $exam->save();
    }

    // Your helper methods (calculateGrade, calculatePoints, etc.) go here
    public function calculateGrade($average)
    {
        if ($average >= 80) {
            return 'A';
        } elseif ($average >= 75) {
            return 'A-';
        } elseif ($average >= 70) {
            return 'B+';
        } elseif ($average >= 65) {
            return 'B';
        } elseif ($average >= 60) {
            return 'B-';
        } elseif ($average >= 55) {
            return 'C+';
        } elseif ($average >= 50) {
            return 'C';
        } elseif ($average >= 45) {
            return 'C-';
        } elseif ($average >= 40) {
            return 'D+';
        } elseif ($average >= 35) {
            return 'D';
        } else {
            return 'E';
        }
    }

    public function calculatePoints($grade)
    {
        $gradeToPointMapping = [
            'A' => 12,
            'A-' => 11,
            'B+' => 10,
            'B' => 9,
            'B-' => 8,
            'C+' => 7,
            'C' => 6,
            'C-' => 5,
            'D+' => 4,
            'D' => 3,
            'E' => 2,
        ];

        return $gradeToPointMapping[$grade] ?? 0;
    }

    public function generateRemarks($grade)
    {    
        $gradeToRemarkMapping = [
            'A' => 'Excellent!',
            'A-' => 'Very good',
            'B+' => 'Good',
            'B' => 'Good',
            'B-' => 'Satisfactory',
            'C+' => 'Satisfactory',
            'C' => 'Average',
            'C-' => 'Average',
            'D+' => 'Below average',
            'D' => 'Below average',
            'E' => 'Poor',
        ];

        return $gradeToRemarkMapping[$grade] ?? '';
   }

   public function calculateSubjectPosition($subjectId, $average, $form)
   {
       // Retrieve exams for the given subject and form
       $studentsExams = Exam::where('subject_id', $subjectId)
           // Retrieve students with the given form (all streams within the form)
           ->whereHas('student', function ($query) use ($form) {
               $query->where('form', $form);
           })->get();

       // Count students with higher average scores
       $higherScores = $studentsExams->filter(function ($exam) use ($average) {
           // Return true if the average score is higher than the given average
           return $exam->average > $average;
       });

       // Position is the count of higher scores plus one
       return $higherScores->count() + 1;
   }
}
