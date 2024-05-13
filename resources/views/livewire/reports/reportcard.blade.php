<?php

use Livewire\Volt\Component;
use App\Models\Exam;
use App\Models\Student;
use App\Models\SchoolSetting;

new class extends Component {
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
    public $schoolSetting;
    public $responsibilities;
    public $clubs;
    public $sports;
    public $houseComment;
    public $teacherComment;
    public $principalComment;
    public $studentActivity;
    public $schoolMotto;
    public $schoolVision;
    // public $useStreams = false;


    public function refreshData()
    {
        $this->updateExamData();
        $this->render();
    }

    // public function downloadPdf()
    // { 
    //     $view = $this->render()->render();
    //     $pdfContent = PDF::loadHTML($view)->setPaper('a4', 'portrait')->output();
    //     ob_end_clean(); // Add this line
    //     return response()->streamDownload(
    //         fn () => print($pdfContent),
    //         $this->student->name . '.pdf'
    //     );
    // }
    
    public function mount($studentId)
    {
        $this->studentId = $studentId;
    
        $this->schoolSetting = SchoolSetting::first(); // assuming there is only one row in the school_settings table
    
        // Get the student record from the database
        $this->student = Student::find($this->studentId);
        if (!$this->student) {
           return;
        }
    
       // Fetch the student activity data
        $this->studentActivity = $this->student->activity;
    
        // Populate the component state with the fetched data
        $this->populateData();
            
        $this->updateExamData();
    
        $averageMark = count($this->exams) ? $this->totalAverage / count($this->exams) : null;
        $this->averageGrade = $averageMark ? $this->calculateGrade($averageMark) : 'N/A';
        $this->schoolMotto = $this->schoolSetting ? $this->schoolSetting->school_motto : null;
        $this->schoolVision = $this->schoolSetting ? $this->schoolSetting->school_vision : null;
    }

    public function populateData()
    {
        if ($this->student && $this->student->activity) {
            $this->responsibilities = $this->studentActivity->responsibilities;
            $this->clubs = $this->studentActivity->clubs;
            $this->sports = $this->studentActivity->sports;
            $this->houseComment = $this->studentActivity->house_comment;
            $this->teacherComment = $this->studentActivity->teacher_comment;
            $this->principalComment = $this->studentActivity->principal_comment;
        } else {
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
        $this->student = Student::find($this->studentId);

        if (!$this->student) {
            return;
        }            
        
        $exams = $this->student->exams()->with('subject')->get();
        $this->exams = $this->student->exams;
        
        $this->emit('refreshComponent');
        
        $this->totalExam1 = $exams->sum('exam1');
        $this->totalExam2 = $exams->sum('exam2');
        $this->totalExam3 = $exams->sum('exam3');
        $this->totalAverage = $exams->sum('average');
        $this->totalPoints = $exams->sum('points');
        
        $examCount = count($this->exams);
        
        if ($examCount > 0) {
            $this->averageExam1 = $this->totalExam1 / $examCount;
            $this->averageExam2 = $this->totalExam2 / $examCount;
            $this->averageExam3 = $this->totalExam3 / $examCount;
            $this->averageTotalAverage = $this->totalAverage / $examCount;
            $this->averageGrade = $this->calculateGrade($this->averageTotalAverage);
        } else {
            $this->averageExam1 = 'N/A';
            $this->averageExam2 = 'N/A';
            $this->averageExam3 = 'N/A';
            $this->averageTotalAverage = 'N/A';
            $this->averageGrade = 'N/A';
        }
        
        $this->useStreams = isset($this->student->stream_id);

        foreach ($exams as $exam) {
            // Apply the same formula as in handle() method
            $averageCATs = ($exam->exam1 + $exam->exam2) / 2;
            $catScore = ($averageCATs / 30) * 30;
            $finalExamScore = ($exam->exam3 / 70) * 70;
            $average = $catScore + $finalExamScore;
            
            $exam->average = round($average);
            $exam->grade = $this->calculateGrade($exam->average);
            $exam->points = $this->calculatePoints($exam->grade);
            $exam->position = $this->calculateSubjectPosition($exam->subject_id, $exam->average, $this->student->form, $this->student->stream_id);
            $exam->remarks = $this->generateRemarks($exam->grade);

            $exam->save();
        }
    }

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

    public function calculateSubjectPosition($subjectId, $average, $form, $streamId = null)
    {
        $studentsExams = Exam::where('subject_id', $subjectId)
            ->whereHas('student', function ($query) use ($form, $streamId) {
                $query->where('form', $form);
                if ($streamId) {
                    $query->where('stream_id', $streamId);
                }
            })->get();

        $higherScores = $studentsExams->filter(function ($exam) use ($average) {
            return $exam->average > $average;
        });

        return $higherScores->count() + 1;
    }

        public function calculateClassPosition($studentId, $totalPoints, $form, $streamId)
        {
            if ($this->useStreams) {
                $students_with_higher_points = Student::where('stream_id', $streamId)
                    ->where(function ($query) use ($totalPoints) {
                        $query->selectRaw('SUM(exams.points)')
                            ->from('exams')
                            ->whereColumn('students.id', 'exams.student_id')
                            ->groupBy('exams.student_id')
                            ->havingRaw('SUM(exams.points) > ?', [$totalPoints]);
                    }, '>', 0)->count();
            
                $students_with_same_points = Student::where('stream_id', $streamId)
                    ->where(function ($query) use ($totalPoints, $studentId) {
                        $query->selectRaw('SUM(exams.points)')
                            ->from('exams')
                            ->whereColumn('students.id', 'exams.student_id')
                            ->where('exams.student_id', '<', $studentId)
                            ->groupBy('exams.student_id')
                            ->havingRaw('SUM(exams.points) = ?', [$totalPoints]);
                    }, '>', 0)->count();
            } else {
                $students_with_higher_points = Student::where('form', $form)
                    ->where(function ($query) use ($totalPoints) {
                        $query->selectRaw('SUM(exams.points)')
                            ->from('exams')
                            ->whereColumn('students.id', 'exams.student_id')
                            ->groupBy('exams.student_id')
                            ->havingRaw('SUM(exams.points) > ?', [$totalPoints]);
                    }, '>', 0)->count();
            
                $students_with_same_points = Student::where('form', $form)
                    ->where(function ($query) use ($totalPoints, $studentId) {
                        $query->selectRaw('SUM(exams.points)')
                            ->from('exams')
                            ->whereColumn('students.id', 'exams.student_id')
                            ->where('exams.student_id', '<', $studentId)
                            ->groupBy('exams.student_id')
                            ->havingRaw('SUM(exams.points) = ?', [$totalPoints]);
                    }, '>', 0)->count();
            }

        $class_position = $students_with_higher_points + $students_with_same_points + 1;

        return $class_position;
    }

        public function calculateStreamPosition($studentId, $totalPoints, $streamId)
        {
            $students_with_higher_points = Student::where('stream_id', $streamId)
                ->where(function ($query) use ($totalPoints) {
                    $query->selectRaw('SUM(exams.points)')
                        ->from('exams')
                        ->whereColumn('students.id', 'exams.student_id')
                        ->groupBy('exams.student_id')
                        ->havingRaw('SUM(exams.points) > ?', [$totalPoints]);
                }, '>', 0)->count();

            $students_with_same_points = Student::where('stream_id', $streamId)
                ->where(function ($query) use ($totalPoints, $studentId) {
                    $query->selectRaw('SUM(exams.points)')
                        ->from('exams')
                        ->whereColumn('students.id', 'exams.student_id')
                        ->where('exams.student_id', '<', $studentId)
                        ->groupBy('exams.student_id')
                        ->havingRaw('SUM(exams.points) = ?', [$totalPoints]);
                }, '>', 0)->count();

                $stream_position = $students_with_higher_points + $students_with_same_points + 1;

                return $stream_position;
        }


        public function calculateOverallPosition($studentId, $totalPoints)
        {
            $student = Student::find($this->studentId);
            if (!$student) {
                return;
            }

        $students_with_higher_points = Student::where('form', $student->form)
            ->where(function ($query) use ($totalPoints) {
                $query->selectRaw('SUM(exams.points)')
                    ->from('exams')
                    ->whereColumn('students.id', 'exams.student_id')
                    ->groupBy('exams.student_id')
                    ->havingRaw('SUM(exams.points) > ?', [$totalPoints]);
            }, '>', 0)->count();

        $students_with_same_points = Student::where('form', $student->form)
            ->where(function ($query) use ($totalPoints, $studentId) {
                $query->selectRaw('SUM(exams.points)')
                    ->from('exams')
                    ->whereColumn('students.id', 'exams.student_id')
                    ->where('exams.student_id', '<', $studentId)
                    ->groupBy('exams.student_id')
                    ->havingRaw('SUM(exams.points) = ?', [$totalPoints]);
            }, '>', 0)->count();

        $overall_position = $students_with_higher_points + $students_with_same_points + 1;

        return $overall_position;
    }

     public function with(): array
     {
        $this->updateExamData();
        $student = Student::with('exams', 'details')->find($this->studentId);

        // Fetch the school setting
        $this->schoolSetting = SchoolSetting::first();

        if (!$student) {
            return view('livewire.report-card', [
                'error_message' => 'No student data found for the given ID',
                'schoolSetting' => $this->schoolSetting, // Pass the school setting to your view
            ]);
        }

        $overallPositions = $this->calculateOverallPosition($student->id, $this->totalPoints);
        $totalStudents = Student::where('form', $student->form)->count();

        $viewData = [
            'student' => $student,
            'overallPositions' => $overallPositions,
            'totalStudents' => $totalStudents,
            'schoolSetting' => $this->schoolSetting, // Pass the school setting to your view
            'schoolMotto' => $this->schoolSetting ? $this->schoolSetting->school_motto : null, // Add the school motto to the view data
            'schoolVision' => $this->schoolSetting ? $this->schoolSetting->school_vision : null, // Add the school vision to the view data
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

        if ($this->useStreams) {
            $streamPositions = $this->calculateStreamPosition($student->id, $this->totalPoints, $student->stream_id);
            $studentsInStream = Student::where('stream_id', $student->stream_id)->count();
            $viewData['streamPositions'] = $streamPositions;
            $viewData['studentsInStream'] = $studentsInStream;
        } else {
            $classPositions = $this->calculateClassPosition($student->id, $this->totalPoints, $student->form, $student->stream_id);
            $studentsInForm = Student::where('form', $student->form)->count();
            $viewData['classPositions'] = $classPositions;
            $viewData['studentsInForm'] = $studentsInForm;
        }
        
        return view('livewire.report-card', array_merge($viewData, ['exams' => $this->exams]));
   }
}; ?>

<div>
    <div class="container mt-5">
        <div class="page-break">
        <div class="document-wrapper">
        <div class="d-flex justify-content-end">
                <button class="btn btn-secondary mb-3" onclick="window.history.back()">Go Back</button>
            </div>
            <button class="print-button" onclick="window.print()">Print this page</button>
            <!-- <button class="print-pdf" wire:click="downloadPdf">Download PDF</button> -->
            <div class="header d-flex flex-column flex-lg-row align-items-center">
            <img class="logo" src="{{ $schoolSetting ? '/storage/' . $schoolSetting->logo_url : '/default-logo.png' }}" alt="School Logo" style="margin-right: 15px;">
                @if (isset($error_message))
                <p>{{ $error_message }}</p>
                @else
                <div class="centered-header-container w-100 text-center">
                    <h3 class="centered-header">{{ $schoolSetting ? $schoolSetting->school_name : 'No School Name' }}</h3>
                    <u><h3 class="centered-header">REPORT FORM FOR TERM {{ $schoolSetting ? $schoolSetting->term : 'N/A' }} {{ $schoolSetting ? $schoolSetting->current_year : 'N/A' }}</h3></u>
                </div>
            </div>
        
                <div class="row">
                <div class="col">
                    <p>Student's Name: {{ $student['name'] }}</p>
                </div>
                <div class="col">
                    <p>ADM No. {{ $student['adm_no'] }}</p>
                </div>
            </div>
                <div class="row underline-row">
                @if (isset($student->stream_id))
                    <div class="col">
                        <p>Stream Position on Points: {{ $streamPositions }} out of {{ $studentsInStream }}</p>
                    </div>
                @else
                    <div class="col">
                        <p>Class Position on Points: {{ $classPositions }} out of {{ $studentsInForm }}</p>
                    </div>
                @endif
                <div class="col">  
                    <p>Overall Position on Points: {{ $overallPositions }} out of {{ $totalStudents }}</p>
                </div>
            </div>
        
            <div class="row primary-details">
            <div class="col-5">
                <p>Primary School Attended: {{ $student->details['primary_school'] ?? '' }}</p>
            </div>
            <div class="col-3">
                <p>Year of KCPE: {{ $student->details['kcpe_year'] ?? '' }}</p>
            </div>
            <div class="col-2">
                <p>KCPE MKs: {{ $student->details['kcpe_marks'] ?? '' }}</p>
            </div>
            <div class="col-2">
                <p>Pos on KCPE: {{ $student->details['kcpe_position'] ?? '' }}</p>
            </div>
        </div>
        
            <div class="centered-table table-responsive">
                    <table class="bordered-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Exam1 (30)</th>
                                <th>Exam2 (30)</th>
                                <th>Exam3 (70)</th>
                                <th>Average (100%)</th>
                                <th>Grade</th>
                                <th>Points</th>
                                <th>Position</th>
                                <th>Remarks</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($exams))
                                @foreach ($exams as $exam)
                                <tr>
                                    <td>{{ $exam->subject->name }}</td>
                                    <td>{{ $exam->exam1 }}</td>
                                    <td>{{ $exam->exam2 }}</td>
                                    <td>{{ $exam->exam3 }}</td>
                                    <td>{{ $exam->average }}</td>
                                    <td>{{ $exam->grade }}</td>
                                    <td>{{ $exam->points }}</td>
                                    <td>{{ $exam->position }}</td>
                                    <td>{{ $exam->remarks }}</td>
                                    <td>{{ $exam->teacher }}</td>
                                </tr>
                                @endforeach
                                <tr style="border-top: 2px solid black;">
                                <td><strong>Total</strong></td>
                                <td>{{ $totalExam1 }}</td>  <!-- Add this line -->
                                <td>{{ $totalExam2 }}</td>
                                <td>{{ $totalExam3 }}</td>
                                <td class="no-border">{{ $totalAverage }}</td>
                                <td class="no-border"></td>
                                <td class="no-border">{{ $totalPoints }}</td>
                                <td class="no-border"></td>
                                <td class="no-border"></td>
                                <td class="no-border"></td>
                            </tr>
                            <tr>
                                <td><strong>Average Mark</strong></td>
                                <td>{{ $averageExam1 }}</td>  <!-- Add this line -->
                                <td>{{ $averageExam2 }}</td>
                                <td>{{ $averageExam3 }}</td>
                                <td class="no-border">{{ $averageTotalAverage }}</td>
                                <td class="no-border">{{ $averageGrade }}</td>
                                <td class="no-border"></td>
                                <td class="no-border"></td>
                                <td class="no-border"></td>
                                <td class="no-border"></td>
                            </tr>
                            <tr style="border-bottom: 2px solid black;"></tr>
                            @else
                            <p>No subjects found for this student.</p>
                            @endif
                        </tbody>
                    </table>
                </div>
        
        
                <div class="below-table-content">
                <div class="row student-extraco-curricular">
                <div class="col ">
                    <h6 class="">Responsibilities</h6>
                    <textarea readonly class="form-control">{{ $student->activity->responsibilities ?? '' }}</textarea>
                </div>
                <div class="col">
                    <h6>Clubs</h6>
                    <textarea readonly class="form-control">{{ $student->activity->clubs ?? '' }}</textarea>
                </div>
                <div class="col">
                    <h6>Sports</h6>
                    <textarea readonly class="form-control">{{ $student->activity->sports ?? '' }}</textarea>
                </div>
                <div class="col">
                    <h6>House Comment</h6>
                    <textarea readonly class="form-control">{{ $student->activity->house_comment ?? '' }}</textarea>
                </div>
            </div>
        
            <div class="row mt-3 ">
                <div class="col-md-6">
                    <h6>Class Teacher's Comments:</h6>
                    <hr>
                    <div class="comment-area">
                        {{ $student->activity->teacher_comment ?? '' }}
                    </div>
                </div>
        
                <div class="col-md-6">
                    <h6>Principal's Comments:</h6>
                    <hr>
                    <div class="comment-area">
                        {{ $student->activity->principal_comment ?? '' }}
                    </div>
                </div>
            </div>
        
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="small-text">School Motto: {{ $schoolSetting ? $schoolSetting->school_motto : '' }}</h6>
                    <h6 class="small-text">School Vision: {{ $schoolSetting ? $schoolSetting->school_vision : '' }}</h6>
                </div>
                <div class="fees">
                    <h6 class="small-text">Fees Balance:</h6>
                    <hr>
                </div>
                    <div class="date">
                    <h6 class="small-text">Closing Date: {{ $schoolSetting ? \Carbon\Carbon::parse($schoolSetting->term_end_date)->format('d/m/Y') : '' }}</h6>
                    <h6 class="small-text">Opening Date: {{ $schoolSetting ? \Carbon\Carbon::parse($schoolSetting->next_term_start_date)->format('d/m/Y') : '' }}</h6>
                </div>
            </div>
            @endif
        </div>
        </div>
        </div>
        </div>        
</div>
