<?php

use Livewire\Volt\Component;
use App\Models\Stream;
use App\Models\ClassForm;
use App\Models\Student;
use Livewire\Attributes\Validate;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $student_name;
    #[Validate('required|unique:students,adm_no')]
    public $adm_no;
    #[Validate('required|exists:class_forms,id')]
    public $class;
    public $stream_id;
    public $class_forms;
    public $streams;
    public $student;
 
    public $term;
   
    public function mount()
    {
        $this->streams = Stream::all();
        $this->class_forms = ClassForm::all();
        $this->class = null;
    }

    // Additional Validation for form mismatch
    public function rules(): array
    {
        return [
            'stream_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $selectedStream = Stream::find($value);
                    if ($selectedStream && substr($selectedStream->name, 5, 1) != $this->class) {
                        $fail('The selected stream does not match the form.');
                    }
                },
            ],
        ];
    }

        public function submit(): void
        {  
            // Validate the input fields
            $this->validate();

            // Find the class with the selected class ID
            $selectedClass = ClassForm::find($this->class);

            // Set the form value based on the selected class or use a default value (e.g., 1)
            $formValue = $selectedClass ? intval(substr($selectedClass->name, -1)) : 1;

            // Find the highest current sequence number for the given form
            $maxSequenceNumber = Student::where('form', $formValue)->max('form_sequence_number');

            // If there's no students in this form yet, start at 1, otherwise increment the max sequence number
            $formSequenceNumber = $maxSequenceNumber ? $maxSequenceNumber + 1 : 1;

            // Create the student record
            $this->student = Student::create([ 
                'name' => $this->student_name,
                'adm_no' => $this->adm_no,
                'stream_id' => $this->stream_id,
                'form' => $formValue,
                'form_sequence_number' => $formSequenceNumber,  
            ]);

        // Dispatch an event to notify student.list component that a student has been created
        $this->dispatch('student-created'); 

        // Show a success message or redirect to another page
        $this->dispatch('success', message: "Student added successfully!");

        // Reset the input fields
        $this->student_name = '';
        $this->adm_no = '';
        $this->class = '';
        $this->stream_id = '';
        $this->term ='';
        }
    }; ?>

<div>
    <div class="p-6 bg-white shadow-md rounded-lg">
        <h2 class="mb-4 text-2xl font-bold">Add Student Details</h2>
    
        <form wire:submit.prevent="submit" class="needs-validation" novalidate>
            <div class="mb-3">
                <input type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="student_name" placeholder="Student Name" required>
                @error('student_name') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <input type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="adm_no" placeholder="Admission Number" required>
                @error('adm_no') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <select class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" wire:model="class" required>
                    <option value="">Select Class</option>
                    @foreach ($class_forms as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <label for="stream_id" class="block text-sm font-medium text-gray-700">Stream</label>
                <select id="stream_id" wire:model="stream_id" class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Select Stream</option>
                    @foreach ($streams as $stream)
                        <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                    @endforeach
                </select>
                @error('stream_id')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
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
    </div>    
</div>
