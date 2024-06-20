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

    // Mount the component
    public function mount()
    {
        $this->students = Student::with('stream', 'classForm')->get();
    }

    public function submit()
    {
        // Validation rules
        $this->validate();

        // Find the student with the selected student ID
        $student = Student::find($this->student_id);

        // Update or create student details
        StudentDetail::updateOrCreate(
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
        $this->reset(['primary_school', 'kcpe_year', 'kcpe_marks', 'kcpe_position', 'student_id']);
    }

    public function with(): array
    {
        return ['students' => $this->students];
    }
}; ?>
<div>
    <div class="container mt-5 p-6 bg-white shadow-md rounded-lg">
        <h2 class="mb-4">Add Student Primary School Details</h2>
        <form wire:submit.prevent="submit" class="needs-validation" novalidate>
            <div class="mb-3">
                <select class="form-select mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" wire:model="student_id" required>
                    <option value="">Select Student</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }} - {{ $student->adm_no }} ({{ $student->classForm->name ?? 'N/A' }}{{ $student->stream->name ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
                @error('student_id') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <input type="text" wire:model="primary_school" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Primary School">
                @error('primary_school') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <input type="text" wire:model="kcpe_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="KCPE Year">
                @error('kcpe_year') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <input type="text" wire:model="kcpe_marks" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="KCPE Marks">
                @error('kcpe_marks') <div class="text-red-500">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <input type="text" wire:model="kcpe_position" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="KCPE Position">
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
