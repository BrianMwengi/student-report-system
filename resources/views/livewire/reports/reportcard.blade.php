<?php

use Livewire\Volt\Component;
use App\Models\Exam;
use App\Models\Student;
use App\Models\SchoolSettings;

new class extends Component {
    // Public properties
    public $student;
    public $studentId;
    public $totalExam1;
    public $totalExam2;
    public $totalExam3;
    public $totalAverage;
    public $totalPoints;
    public $useStreams;
    public $exams;
    public $averageGrade;
    public $averageExam1; 
    public $averageExam2;
    public $averageExam3;
    public $averageTotalAverage;
    public $schoolSettings;
    public $responsibilities;
    public $clubs;
    public $sports;
    public $houseComment;
    public $teacherComment;
    public $principalComment;
    public $studentActivity;
    public $schoolMotto;
    public $schoolVision;


    // Mount the component
    public function mount($studentId)
    {
        // Fetch the student ID
        $this->studentId = $studentId;
        // Fetch the school setting
        $this->schoolSettings = SchoolSettings::first(); // assuming there is only one row in the school_settings table

        // Get the student record from the database
        $this->student = Student::find($this->studentId);
        // if the student record is not found, return
        if (!$this->student) {
            return;
        }

        // Fetch the student activity data
        $this->studentActivity = $this->student->activity;

        // Populate the component state with the fetched data
        $this->populateData();
        // Update the exam data     
        $this->updateExamData();

        // if the student has exams, calculate the average mark
        if (count($this->exams) > 0) {
            $averageMark = $this->totalAverage / count($this->exams);
        } else {
            $averageMark = null;
        }

        // if the average mark is not null, calculate the average grade
        if ($averageMark !== null) {
            $this->averageGrade = $this->calculateGrade($averageMark);
        } else {
            $this->averageGrade = 'N/A';
        }

        // if school settings are available, set the school motto and vision
        if ($this->schoolSettings) {
            $this->schoolMotto = $this->schoolSettings->school_motto;
            $this->schoolVision = $this->schoolSettings->school_vision;
        } else {
            $this->schoolMotto = null;
            $this->schoolVision = null;
        }

    }

    // Populate the component state with the fetched data
    public function populateData()
    {
        // Check if student and student activity exist
        if ($this->student && $this->student->activity) {
            // Populate activity-related data
            $this->responsibilities = $this->studentActivity->responsibilities;
            $this->clubs = $this->studentActivity->clubs;
            $this->sports = $this->studentActivity->sports;
            $this->houseComment = $this->studentActivity->house_comment;
            $this->teacherComment = $this->studentActivity->teacher_comment;
            $this->principalComment = $this->studentActivity->principal_comment;
        } else {
            // Set default values when no activity data is found
            $this->responsibilities = 'N/A';
            $this->clubs = 'N/A';
            $this->sports = 'N/A';
            $this->houseComment = 'N/A';
            $this->teacherComment = 'N/A';
            $this->principalComment = 'N/A';
        }
    }

    public function updateExamData()
    {
        // Find student by ID
        $this->student = Student::find($this->studentId);

        // If student not found, return early
        if (!$this->student) {
            return;
        }            

        // Retrieve exams with associated subjects
        $exams = $this->student->exams()->with('subject')->get();
        $this->exams = $this->student->exams;        

        // Calculate total scores for exams
        $this->totalExam1 = $exams->sum('exam1');
        $this->totalExam2 = $exams->sum('exam2');
        $this->totalExam3 = $exams->sum('exam3');
        $this->totalAverage = $exams->sum('average');
        $this->totalPoints = $exams->sum('points');

        $examCount = count($this->exams);

        // Calculate average scores if exams exist
        if ($examCount > 0) {
            $this->averageExam1 = round($this->totalExam1 / $examCount, 2);
            $this->averageExam2 = round($this->totalExam2 / $examCount, 2);
            $this->averageExam3 = round($this->totalExam3 / $examCount, 2);
            $this->averageTotalAverage = round($this->totalAverage / $examCount, 2);
            $this->averageGrade = $this->calculateGrade($this->averageTotalAverage);
        } else {
            // Set default values when no exams are found
            $this->averageExam1 = 'N/A';
            $this->averageExam2 = 'N/A';
            $this->averageExam3 = 'N/A';
            $this->averageTotalAverage = 'N/A';
            $this->averageGrade = 'N/A';
        }
        // Check if student is assigned to a stream
        $this->useStreams = isset($this->student->stream_id);

        // If the student doesn't have a stream, show an error message
        if (!$this->useStreams) {
            session()->flash('error', 'The student is not assigned to a stream.');
        }

        // Calculate details for each exam
        foreach ($exams as $exam) {
            // Apply the same formula as in handle() method
            $averageCATs = ($exam->exam1 + $exam->exam2) / 2;
            $catScore = ($averageCATs / 30) * 30;
            $finalExamScore = ($exam->exam3 / 70) * 70;
            $average = $catScore + $finalExamScore;

            // Round the average score
            $exam->average = round($average);
            // Calculate grade, points, position, and remarks
            $exam->grade = $this->calculateGrade($exam->average);
            // Get points for the grade based on the grade
            $exam->points = $this->calculatePoints($exam->grade);
            // To get student subject position, pass the student's form, average score, and stream ID
            $exam->position = $this->calculateSubjectPosition($exam->subject_id, $exam->average, $this->student->form, $this->student->stream_id);
            // Generate remarks based on the grade
            $exam->remarks = $this->generateRemarks($exam->grade);

            // Save the updated exam details
            $exam->save();
        }
    }

    public function calculateGrade($average)
    {
        // Determine grade based on average score
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
        // Map grades to points
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

        // Return the corresponding points for the grade, or 0 if not found
        return $gradeToPointMapping[$grade] ?? 0;
    }

    public function generateRemarks($grade)
    {
        // Map grades to remarks
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

        // Return the corresponding remark for the grade, or an empty string if not found
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


    public function calculateStreamPosition($studentId, $totalPoints, $streamId)
    {
        // Count students with higher points in the same stream
        $students_with_higher_points = Student::where('stream_id', $streamId)
            ->where(function ($query) use ($totalPoints) {
                $query->selectRaw('SUM(exams.points)')
                    ->from('exams')
                    ->whereColumn('students.id', 'exams.student_id')
                    ->groupBy('exams.student_id')
                    ->havingRaw('SUM(exams.points) > ?', [$totalPoints]);
            }, '>', 0)->count();

        // Count students with the same points in the same stream
        $students_with_same_points = Student::where('stream_id', $streamId)
            ->where(function ($query) use ($totalPoints) {
                $query->selectRaw('SUM(exams.points)')
                    ->from('exams')
                    ->whereColumn('students.id', 'exams.student_id')
                    ->groupBy('exams.student_id')
                    ->havingRaw('SUM(exams.points) = ?', [$totalPoints]);
            }, '>', 0)->count();

        // Calculate stream position
        $stream_position = $students_with_higher_points + 1;

        return $stream_position;
    }


    public function calculateOverallPosition($studentId, $totalPoints)
    {
        // Find the student by ID
        $student = Student::find($this->studentId);
        
        // If the student is not found, return early
        if (!$student) {
            return;
        }

        // Query to count students with higher points in the same form
        $students_with_higher_points = Student::where('form', $student->form)
            ->whereHas('exams', function ($query) use ($totalPoints) {
                $query->selectRaw('SUM(points) as total_points')
                    ->groupBy('student_id')
                    ->havingRaw('total_points > ?', [$totalPoints]);
            })->count();

        // Query to count students with the same points but lower student IDs
        $students_with_same_points = Student::where('form', $student->form)
            ->whereHas('exams', function ($query) use ($totalPoints, $studentId) {
                $query->selectRaw('SUM(points) as total_points')
                    ->groupBy('student_id')
                    ->havingRaw('total_points = ?', [$totalPoints]);
            })
            ->where('id', '<', $studentId) // Filter to include only students with lower IDs
            ->count();

        // Calculate the overall position
        $overall_position = $students_with_higher_points + $students_with_same_points + 1;

        return $overall_position;
    }


    // Refresh the data
    public function refreshData()
    {
        // Fetch the student record from the database
        $this->updateExamData();
        $this->render();
    }

    public function with(): array
    {
        // Update exam data for the current student
        $this->updateExamData();

        // Find the student by ID along with related exams and details
        $student = Student::with('exams', 'details')->find($this->studentId);

        // Fetch the school settings
        $this->schoolSettings = SchoolSettings::first();

        // If no student data is found, return an error message
        if (!$student) {
            return [
                'error_message' => 'No student data found for the given ID',
                'schoolSettings' => $this->schoolSettings, // Pass the school settings to the view
            ];
        }

        // Calculate the student's overall position based on total points
        $overallPositions = $this->calculateOverallPosition($student->id, $this->totalPoints);
        // Count the total number of students in the same form
        $totalStudents = Student::where('form', $student->form)->count();

        // Prepare the view data array
        $viewData = [
            'student' => $student,
            'overallPositions' => $overallPositions,
            'totalStudents' => $totalStudents,
            'schoolSettings' => $this->schoolSettings, // Pass the school settings to the view
            'schoolMotto' => $this->schoolSettings ? $this->schoolSettings->school_motto : null, // Add the school motto to the view data
            'schoolVision' => $this->schoolSettings ? $this->schoolSettings->school_vision : null, // Add the school vision to the view data
            'totalExam1' => $this->totalExam1,
            'totalExam2' => $this->totalExam2,
            'totalExam3' => $this->totalExam3,
            'totalAverage' => $this->totalAverage,
            'totalPoints' => $this->totalPoints,
            'averageExam1' => $this->averageExam1,
            'averageExam2' => $this->averageExam2,
            'averageExam3' => $this->averageExam3,
            'averageTotalAverage' => $this->averageTotalAverage,
            'averageGrade' => $this->averageGrade,
        ];

        // Calculate the student's position within their stream
        $streamPositions = $this->calculateStreamPosition($student->id, $this->totalPoints, $student->stream_id);
        // Count the total number of students in the same stream
        $studentsInStream = Student::where('stream_id', $student->stream_id)->count();
        
        // Add stream positions and total students in the stream to the view data
        $viewData['streamPositions'] = $streamPositions;
        $viewData['studentsInStream'] = $studentsInStream;

        // Merge exams data with view data and return
        return array_merge($viewData, ['exams' => $this->exams]);
    }
}; ?>


<div class="container mx-auto max-w-5xl">
    <div class="page-break">
        <div class="document-wrapper">
            <div class="flex justify-end">
                <button class="bg-gray-400 text-white px-2 py-2 mb-3 go-back go-back-button" onclick="window.history.back()">Go Back</button>
                <button class="bg-blue-500 text-white px-2 py-2 mb-3 ml-2 print-button" onclick="window.print()">Print this page</button>
            </div>
            <div class="header flex flex-col items-center">
                <img class="logo mb-4" src="{{ $schoolSettings ? '/storage/' . $schoolSettings->logo_url : '/default-logo.png' }}" alt="School Logo">
                @if (isset($error_message))
                    <p>{{ $error_message }}</p>
                @else
                    <div class="centered-header-container w-full text-center">
                        <h3 class="centered-header text-xl">{{ $schoolSettings ? $schoolSettings->school_name : 'No School Name' }}</h3>
                        <u><h3 class="centered-header text-xl">REPORT FORM FOR TERM {{ $schoolSettings ? $schoolSettings->term : 'N/A' }} {{ $schoolSettings ? $schoolSettings->current_year : 'N/A' }}</h3></u>
                    </div>
                @endif
            </div>
  
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p>Student's Name: {{ $student['name'] }}</p>
          </div>
          <div>
            <p>ADM No. {{ $student['adm_no'] }}</p>
          </div>
        </div>
        <div class="grid grid-cols-2 border-b border-gray-400 pb-2">
          @if (isset($student->stream_id))
            <div>
              <p>Stream Position on Points: {{ $streamPositions }} out of {{ $studentsInStream }}</p>
            </div>
          @else
            <div>
              <p>Class Position on Points: {{ $classPositions }} out of {{ $studentsInForm }}</p>
            </div>
          @endif
          <div>
            <p>Overall Position on Points: {{ $overallPositions }} out of {{ $totalStudents }}</p>
          </div>
        </div>
  
        <div class="grid grid-cols-5 gap-4 primary-details mt-3">
            <div class="col-span-2">
                <p>Primary School Attended: {{ $student->details['primary_school'] ?? '' }}</p>
            </div>
            <div class="col-span-1">
                <p>Year of KCPE: {{ $student->details['kcpe_year'] ?? '' }}</p>
            </div>
            <div class="col-span-1">
                <p>KCPE MKs: {{ $student->details['kcpe_marks'] ?? '' }}</p>
            </div>
            <div class="col-span-1">
                <p>Pos on KCPE: {{ $student->details['kcpe_position'] ?? '' }}</p>
            </div>
        </div>
  
        <div class="centered-table table-responsive">
          <table class="w-full border border-gray-400 bordered-table">
            <thead>
              <tr class="bg-gray-200">
                <th class="border border-gray-400 px-2 py-2">Subject</th>
                <th class="border border-gray-400 px-2 py-2">Exam1 (30)</th>
                <th class="border border-gray-400 px-2 py-2">Exam2 (30)</th>
                <th class="border border-gray-400 px-2 py-2">Exam3 (70)</th>
                <th class="border border-gray-400 px-2 py-2">Average (100%)</th>
                <th class="border border-gray-400 px-2 py-2">Grade</th>
                <th class="border border-gray-400 px-2 py-2">Points</th>
                <th class="border border-gray-400 px-2 py-2">Position</th>
                <th class="border border-gray-400 px-2 py-2">Remarks</th>
                <th class="border border-gray-400 px-2 py-2">Teacher</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($exams))
                @foreach ($exams as $exam)
                  <tr>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->subject->name }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->exam1 }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->exam2 }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->exam3 }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->average }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->grade }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->points }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->position }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->remarks }}</td>
                    <td class="border border-gray-400 px-2 py-2">{{ $exam->teacher }}</td>
                  </tr>
                @endforeach
                <tr class="border-b border-gray-400">
                  <td class="border border-gray-400 px-2 py-2"><strong>Total</strong></td>
                  <td class="border border-gray-400 px-2 py-2">{{ $totalExam1 }}</td>
                  <td class="border border-gray-400 px-2 py-2">{{ $totalExam2 }}</td>
                  <td class="border border-gray-400 px-2 py-2">{{ $totalExam3 }}</td>
                  <td class="px-2 py-2">{{ $totalAverage }}</td>
                  <td class="px-2 py-2"></td>
                  <td class="px-2 py-2">{{ $totalPoints }}</td>
                  <td class="px-2 py-2"></td>
                  <td class="px-2 py-2"></td>
                  <td class="px-2 py-2"></td>
                </tr>
                <tr>
                  <td class="border border-gray-400 px-2 py-2"><strong>Average Mark</strong></td>
                  <td class="border border-gray-400 px-2 py-2">{{ $averageExam1 }}</td>
                  <td class="border border-gray-400 px-2 py-2">{{ $averageExam2 }}</td>
                  <td class="border border-gray-400 px-2 py-2">{{ $averageExam3 }}</td>
                  <td class="px-2 py-2">{{ $averageTotalAverage }}</td>
                  <td class="px-2 py-2">{{ $averageGrade }}</td>
                  <td class="px-2 py-2"></td>
                  <td class="px-2 py-2"></td>
                  <td class="px-2 py-2"></td>
                  <td class="px-2 py-2"></td>
                </tr>
                <tr class="border-b border-gray-400"></tr>
              @else
                <p>No subjects found for this student.</p>
              @endif
            </tbody>
          </table>
        </div>
  
        <div class="below-table-content">
            <div class="grid grid-cols-4 gap-2 student-extraco-curricular">
                <div>
                    <h6 class="">Responsibilities</h6>
                    <textarea readonly class="w-full p-2 border rounded">{{ $student->activity->responsibilities ?? '' }}</textarea>
                </div>
                <div>
                    <h6>Clubs</h6>
                    <textarea readonly class="w-full p-2 border rounded">{{ $student->activity->clubs ?? '' }}</textarea>
                </div>
                <div>
                    <h6>Sports</h6>
                    <textarea readonly class="w-full p-2 border rounded">{{ $student->activity->sports ?? '' }}</textarea>
                </div>
                <div>
                    <h6>House Comment</h6>
                    <textarea readonly class="w-full p-2 border rounded">{{ $student->activity->house_comment ?? '' }}</textarea>
                </div>
            </div>
        </div>      
        
        <div class="grid grid-cols-2 gap-4 mt-3">
            <div>
              <h6>Class Teacher's Comments:</h6>
              <hr>
              <div class="comment-area">
                {{ $student->activity->teacher_comment ?? '' }}
              </div>
            </div>
            <div>
              <h6>Principal's Comments:</h6>
              <hr>
              <div class="comment-area">
                {{ $student->activity->principal_comment ?? '' }}
              </div>
            </div>
          </div>
  
          <div class="flex justify-between">
            <div>
              <h6 class="text-sm">School Motto: {{ $schoolSettings ? $schoolSettings->school_motto : '' }}</h6>
              <h6 class="text-sm">School Vision: {{ $schoolSettings ? $schoolSettings->school_vision : '' }}</h6>
            </div>
            <div class="fees">
              <h6 class="text-sm">Fees Balance:</h6>
              <hr>
            </div>
            <div class="date">
              <h6 class="text-sm">Closing Date: {{ $schoolSettings ? \Carbon\Carbon::parse($schoolSettings->term_end_date)->format('d/m/Y') : '' }}</h6>
              <h6 class="text-sm">Opening Date: {{ $schoolSettings ? \Carbon\Carbon::parse($schoolSettings->next_term_start_date)->format('d/m/Y') : '' }}</h6>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>