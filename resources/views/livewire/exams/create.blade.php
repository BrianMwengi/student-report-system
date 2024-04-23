<?php

use Livewire\Volt\Component;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Student;

new class extends Component {
    #[Validate('required|exists:students,adm_no')]
    public $adm_no;
    #[Validate('required|exists:subjects,id')]             
    public $subject_id;
    #[Validate('required|numeric')]
    public $exam1;
    #[Validate('required|numeric')]
    public $exam2;
    #[Validate('required|numeric')]
    public $exam3;
    #[Validate('required|string|max:255')]
    public $teacher;
    public $subjects;
    public $student_name;
    public $class;
    public $studentDetails;
    public $stream_id;

    
    public function mount()
    {
        $this->subjects = Subject::all();
        $this->subject_id = $this->subjects->first() ? $this->subjects->first()->id : null;
    }

    public function submit()
    {
        // Validate the input data
        $this->validate();
        // Check if the student with the given admission number exists
        $student = Student::where('adm_no', $this->adm_no)->first();

        // Check if the subject already exists for the student
        $existingExam = Exam::where('student_id', $student->id)
                        ->where('subject_id', $this->subject_id)
                        ->exists();

        if ($existingExam) {
            session()->flash('error', 'This subject has already been added for this student. Please choose a different subject.');
            return;
        }

        // Save the exam results
        $exam = Exam::create([
            'student_id' => $student->id,
            'subject_id' => $this->subject_id,
            'exam1' => $this->exam1,
            'exam2' => $this->exam2,
            'exam3' => $this->exam3,
            'teacher' => $this->teacher,
        ]);

        $this->subject_id = '';
        $this->exam1 = '';
        $this->exam2 = '';
        $this->exam3 = '';
        $this->teacher = '';
}; ?>

<div>
    //
</div>
