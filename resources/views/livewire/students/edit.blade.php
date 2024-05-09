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
    public Student $student;
    
    // Initialize the component
    public function mount($id): void
    {
        // Find the student with the given ID
        $this->student = Student::find($id);
        // if the student is found, set the properties
        if ($this->student) {
            $this->studentId = $this->student->id;
            $this->form_sequence_number = $this->student->form_sequence_number;
            $this->name = $this->student->name;
            $this->adm_no = $this->student->adm_no;
            $this->form = $this->student->form; 
            $this->stream_id = $this->student->stream_id;
            
            // Get the student details
            $this->studentDetailsId = $this->student->details;
            $this->studentDetails = $this->student->details;
            // if the student details are found, set the properties
            if ($this->studentDetails) {
                $this->primary_school = $this->studentDetails->primary_school;
                $this->kcpe_marks = $this->studentDetails->kcpe_marks;
                $this->kcpe_year = $this->studentDetails->kcpe_year;
                $this->kcpe_position = $this->studentDetails->kcpe_position;
            }

        // Find the exam details for the student    
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

        // Get all the classes, streams and subjects
        $this->classes = ClassForm::all();
        $this->streams = Stream::all();
        $this->subjects = Subject::all();
        $this->subject_id = $this->subjects->first()->id;
    }

    // Method to update the student details
    public function updateStudent(): void
    { 
        $validatedData = $this->validate();
        // If the student ID is set, update the student details
        if ($this->studentId) {
            $student = Student::find($this->studentId);
            $student->update($validatedData);
            
            // Find the student details
            $studentDetails = StudentDetail::where('student_id', $this->studentId)->first();
            // If the student details are found, update the details
            if ($studentDetails) {
                // empty array to hold the KCPE details
                $kcpeDetails = [];
                if ($this->primary_school) {
                    $kcpeDetails['primary_school'] = $this->primary_school;
                }
                if ($this->kcpe_marks) {
                    $kcpeDetails['kcpe_marks'] = $this->kcpe_marks;
                }
                if ($this->kcpe_year) {
                    $kcpeDetails['kcpe_year'] = $this->kcpe_year;
                }
                if ($this->kcpe_position) {
                    $kcpeDetails['kcpe_position'] = $this->kcpe_position;
                }
                // If the KCPE details are not empty, update the student details
                if (!empty($kcpeDetails)) {
                    $studentDetails->update($kcpeDetails);
                }
            }
    
            // Update the Exam model
            Exam::updateOrCreate(
                // Find the exam details for the student
                ['id' => $this->examId, 'student_id' => $this->studentId, 'subject_id' => $this->subject_id],
                [
                    'subject_id' => $this->subject_id,
                    'exam1' => $this->exam1,
                    'exam2' => $this->exam2,
                    'exam3' => $this->exam3,
                    'teacher' => $this->teacher,
                ]
            );
    
            // If the update was successful, flash a success message
            session()->flash('success', 'Student updated successfully');
            // Dispatch an event to update the student list
            $this->dispatch('student-updated');
    
        } else {
            session()->flash('error', 'Failed to update student details.');
        }
    }
    
    // Method to update the exam details
    public function updatedSubjectId()
    {  
        // If the subject ID is set, get the exam details
        if ($this->subject_id) {
            // Find the exam details for the student
            $exam = Exam::where('student_id', $this->studentId)->where('subject_id', $this->subject_id)->first();
            // If the exam details are found, set the properties
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

    // Reset the exam fields
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
       <form class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <div class="mb-3">
                <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                <input type="text" id="name" wire:model="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <label for="adm_no" class="block text-sm font-medium text-gray-700">Admission Number:</label>
                <input type="text" id="adm_no" wire:model="adm_no" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('adm_no') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <label for="primary_school" class="block text-sm font-medium text-gray-700">Primary School:</label>
                <input type="text" id="primary_school" wire:model="primary_school" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            
            <div class="mb-3">
                <label for="kcpe_year" class="block text-sm font-medium text-gray-700">KCPE Year:</label>
                <input type="number" id="kcpe_year" wire:model="kcpe_year" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            
            <div class="mb-3">
                <label for="kcpe_marks" class="block text-sm font-medium text-gray-700">KCPE Marks:</label>
                <input type="number" id="kcpe_marks" wire:model="kcpe_marks" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
    
            <div class="mb-3">
                <label for="kcpe_position" class="block text-sm font-medium text-gray-700">KCPE Position:</label>
                <input type="number" id="kcpe_position" wire:model="kcpe_position" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
    
            <div class="mb-3">
                <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject:</label>
                <select id="subject_id" wire:model="subject_id" class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">-- Select Subject --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="mb-3">
                <label for="exam1" class="block text-sm font-medium text-gray-700">Exam 1:</label>
                <input type="number" id="exam1" wire:model="exam1" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('exam1') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="mb-3">
                <label for="exam2" class="block text-sm font-medium text-gray-700">Exam 2:</label>
                <input type="number" id="exam2" wire:model="exam2" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('exam2') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="mb-3">
                <label for="exam3" class="block text-sm font-medium text-gray-700">Exam 3:</label>
                <input type="number" id="exam3" wire:model="exam3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('exam3') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="mb-3">
                <label for="teacher" class="block text-sm font-medium text-gray-700">Teacher:</label>
                <input type="text" id="teacher" wire:model="teacher" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                @error('teacher') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="mb-3">
                <label for="form" class="block text-sm font-medium text-gray-700">Form:</label>
                <select wire:model="form" class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Select Form</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('form') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="mb-3">
                <label for="stream_id" class="block text-sm font-medium text-gray-700">Stream:</label>
                <select wire:model="stream_id" class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Select Stream</option>
                    @foreach ($streams as $stream)
                        <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                    @endforeach
                </select>
                @error('stream_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
    
            <div class="mb-3">
                <button wire:click="updateStudent" wire:loading.attr="disabled" wire:target="updateStudent" class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Update Student</button>
            </div>
        </form>
        
        {{-- Flash success message --}}
        @if (session('success'))
        <div x-data="{ open: true }" 
        x-init="setTimeout(() => open = false, 4000)"
        x-show="open"
        class="mt-4 bg-green-500 text-white font-bold py-2 px-4 rounded">
        {{ session('success') }}
        </div>
        @endif

        {{-- Flash error message --}}
        @if (session('error'))
        <div x-data="{ open: true }" 
        x-init="setTimeout(() => open = false, 4000)"
        x-show="open"
        class="mt-4 bg-red-500 text-white font-bold py-2 px-4 rounded">
        {{ session('error') }}
        </div>
        @endif   
    </div>
