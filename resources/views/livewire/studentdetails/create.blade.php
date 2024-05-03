<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\StudentDetail;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $primary_school;
    #[Validate('required|integer')]
    public $kcpe_year;
    #[Validate('required|integer')]
    public $kcpe_marks;
    #[Validate('required|integer')]
    public $kcpe_position;
    public $student_id;
    public $students;
    public $adm_no;

    // Mount the students
    public function mount()
    {
        $this->students = Student::all();
    }

    // Get the student details
    public function getStudent()
    {
        return Student::find($this->student_id);
    }

    // Update the admission number
    public function updateAdmissionNumber()
    {
        $student = $this->getStudent(); 
        $this->adm_no = $student ? $student->adm_no : '';
    }

    public function submit()
    {
        // Validation rules
        $this->validate();

         // Find the student with the selected student ID
        $student = $this->getStudent();

        // Set the student_id property to the student's id 
        $this->student_id = $student->id;

        // Update or create student details
        $studentDetails = StudentDetail::updateOrCreate(
            ['student_id' => $this->student_id],
            [
                'primary_school' => $this->primary_school,
                'kcpe_year' => $this->kcpe_year,
                'kcpe_marks' => $this->kcpe_marks,
                'kcpe_position' => $this->kcpe_position,
            ]
        );

         // Show a success message or redirect to another page
         $this->dispatch('success', message: "Student detail added successfully!");
        // Reset input fields
        $this->student_id = '';
        $this->adm_no = '';
        $this->primary_school = '';
        $this->kcpe_year = '';
        $this->kcpe_marks = '';
        $this->kcpe_position = '';
    }
}; ?>

<div>
    <div class="container mt-5 p-6 bg-white shadow-md rounded-lg">
        <h2 class="mb-4">Add Student Primary School Details</h2>
        <form wire:submit.prevent="submit" class="needs-validation" novalidate>
            <div class="mb-3">
                <select class="form-select" wire:model="student_id" wire:change="updateAdmissionNumber" required>
                    <option value="">Select Student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                    @endforeach
                </select>
                @error('student') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>
        
            <div class="mb-3">
                <input type="text" wire:model="adm_no" class="form-input" placeholder="Admission Number" readonly>
                @error('adm_no') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
        
            <div class="mb-3">
                <input type="text" wire:model="primary_school" class="form-input" placeholder="Primary School" value="{{ $primary_school }}">
                @error('primary_school') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <input type="text" wire:model="kcpe_year" class="form-input" placeholder="KCPE Year" value="{{ $kcpe_year }}">
                @error('kcpe_year') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <input type="text" wire:model="kcpe_marks" class="form-input" placeholder="KCPE Marks" value="{{ $kcpe_marks }}">
                @error('kcpe_marks') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <input type="text" wire:model="kcpe_position" class="form-input" placeholder="KCPE Position" value="{{ $kcpe_position }}">
                @error('kcpe_position') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
        </form>
        <div x-data="{ open: false, message: '' }" 
             x-cloak
        @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 4000)"
        x-show="open"
        class="mt-4 bg-green-500 text-white font-bold py-2 px-4 rounded">
        <span x-text="message"></span>
</div>
    </div>    
</div>
