<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\Exam;
use App\Models\ClassForm;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\StudentDetail;
use Livewire\Attributes\Validate;

new class extends Component {
    public $studentId;
    #[Validate('required')]
    public $name;
    #[Validate('required')]
    public $adm_no;
    #[Validate('required')]
    public $form;
    #[Validate('required')]
    public $stream_id;
    #[Validate('required')]
    public $exam1;
    #[Validate('required')]
    public $exam2;
    #[Validate('required')]
    public $exam3;
    #[Validate('required')]
    public $teacher;
    #[Validate('required')]
    public $subject_id;
    public $subjects;
    public $primary_school;
    public $kcpe_year;
    public $kcpe_marks;
    public $kcpe_position;
    public $classes;
    public $streams;
    public $studentDetailsId;
    public $examId;
    public $form_sequence_number;
    
    public function mount($id)
    { 
        $student = Student::find($id);

        if ($student) {
            $this->studentId = $student->id;
            $this->form_sequence_number = $student->form_sequence_number;
            $this->name = $student->name;
            $this->adm_no = $student->adm_no;
            $this->form = $student->class_id; // assuming the student has a class_id field linking to the classes table
            $this->stream_id = $student->stream_id;

        $studentDetails = $student->details;
        if ($studentDetails) {
            $this->studentDetailsId = $studentDetails->id;
            $this->primary_school = $studentDetails->primary_school;
            $this->kcpe_year = $studentDetails->kcpe_year;
            $this->kcpe_marks = $studentDetails->kcpe_marks;
            $this->kcpe_position = $studentDetails->kcpe_position;
        }

        $exam = Exam::where('student_id', $this->studentId)->first();
        if ($exam) {
            $this->exam1 = $exam->exam1;
            $this->exam2 = $exam->exam2;
            $this->exam3 = $exam->exam3;
            $this->teacher = $exam->teacher;
            $this->subject_id = $exam->subject_id;
            $this->examId = $exam->id;
        }
    }

    $this->classes = ClassForm::all();
    $this->streams = Stream::all();
    $this->subjects = Subject::all();
    $this->subject_id = $this->subjects->first()->id;
}

public function updateStudent()
{ 
    $validatedData = $this->validate();

    $validatedData['form'] = $this->form;
    $validatedData['stream_id'] = $streamIdValue;

    $student = Student::find($this->studentId);

    if ($student) {
        // Update the Student model
        $student->update($validatedData);

            // Update the StudentDetail model
            StudentDetail::updateOrCreate(
                ['id' => $this->studentDetailsId, 'student_id' => $this->studentId],
                [
                    'primary_school' => $this->primary_school,
                    'kcpe_year' => $this->kcpe_year,
                    'kcpe_marks' => $this->kcpe_marks,
                    'kcpe_position' => $this->kcpe_position,
                ]
            );
            
            // Update the Exam model
            Exam::updateOrCreate(
                ['id' => $this->examId, 'student_id' => $this->studentId, 'subject_id' => $this->subject_id],
                [
                    'subject_id' => $this->subject_id,
                    'exam1' => $this->exam1,
                    'exam2' => $this->exam2,
                    'exam3' => $this->exam3,
                    'teacher' => $this->teacher,
                ]
            );
  
    session()->flash('message', 'Student details updated successfully.');
            
    } else {
        session()->flash('error_message', 'Failed to update student details.');
    }
}

public function updatedSubjectId()
{  
    if ($this->subject_id) {
        $exam = Exam::where('student_id', $this->studentId)->where('subject_id', $this->subject_id)->first();
        if ($exam) {
            $this->exam1 = $exam->exam1;
            $this->exam2 = $exam->exam2;
            $this->exam3 = $exam->exam3;
            $this->teacher = $exam->teacher;
            $this->examId = $exam->id;
        } else {
            $this->resetExamFields();
        }
    } else {
        $this->resetExamFields();
    }
}

private function resetExamFields()
{
    $this->exam1 = '';
    $this->exam2 = '';
    $this->exam3 = '';
    $this->teacher = '';
    $this->examId = null;
}
    
}; ?>

<div>
    //
</div>
