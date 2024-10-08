<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
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
    public $students;
    public $subjects;
    public $studentDetails;

    // Mount the component
    public function mount()
    {
        // Get all subjects
        $this->subjects = Subject::all();
        // Get the first subject id
        $this->subject_id = $this->subjects->first() ? $this->subjects->first()->id : null;
        // Fetch all students with form and stream
        $this->students = Student::with('stream', 'classForm')->get();
    }

    // Update the student details
    public function updatedAdmNo($adm_no)
    {
        // Fetch the student details including form and stream
        $this->studentDetails = Student::where('adm_no', $adm_no)->with('stream', 'classForm')->first();
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

        // Show a success message or redirect to another page
        $this->dispatch('success', message: "Exam details added successfully!");
        
        // Reset the input fields
        $this->reset(['subject_id', 'exam1', 'exam2', 'exam3', 'teacher', 'studentDetails']);
    }
}; ?>
<div>
    <div class="container mt-5 p-6 bg-white shadow-md rounded-lg">
        <h2 class="mb-4 text-2xl font-bold">Add Exam Details</h2>
        <form wire:submit.prevent="submit" class="needs-validation" novalidate>
            <div class="mb-3">
                <label for="studentSelect" class="form-label">Select Student</label>
                <select id="studentSelect" class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="adm_no" required>
                    <option value="">Select a Student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->adm_no }}">
                            {{ $student->name }} - {{ $student->adm_no }} ({{ $student->form }}{{ $student->stream->name }})
                        </option>
                    @endforeach
                </select>
                @error('adm_no') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            @if ($studentDetails)
                <div class="mb-3">
                    <label class="form-label">Selected Student Details:</label>
                    <div>
                        Name: {{ $studentDetails->name }}<br>
                        Admission Number: {{ $studentDetails->adm_no }}<br>
                        Form: {{ $studentDetails->form }}<br>
                        Stream: {{ $studentDetails->stream->name }}
                    </div>
                </div>
            @endif

            <div class="mb-3">
                <select class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="subject_id" required>
                    <option value="">Select Subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <input type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="exam1" placeholder="Exam1 (30)" required>
                @error('exam1') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <input type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="exam2" placeholder="Exam2 (30)" required>
                @error('exam2') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <input type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="exam3" placeholder="Exam3 (70)" required>
                @error('exam3') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <input type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="teacher" placeholder="Teacher" required>
                @error('teacher') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
            </div>
        </form>

        <div x-data="{ open: false, message: '' }" 
             x-cloak
             @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 4000)"
             x-show="open"
             class="mt-4 bg-green-500 text-white font-bold py-2 px-4 rounded">
            <span x-text="message"></span>
        </div>

        @if (session('error'))
            <div x-data="{ open: true }" 
                 x-init="setTimeout(() => open = false, 4000)"
                 x-show="open"
                 class="mt-4 bg-red-500 text-white font-bold py-2 px-4 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>
</div>
