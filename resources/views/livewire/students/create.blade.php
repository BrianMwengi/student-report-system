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

    public $class_forms;
    public $streams;
    public $student;
    public $stream_id;
    public $term;
   
    public function mount()
    {
        $this->streams = Stream::all();
        $this->class_forms = ClassForm::all();
        $this->class = null;
    }

        public function submit()
        {  
            // Validate the input fields
            $this->validate();

            // Create the student record
            $this->student = Student::create([ 
                'name' => $this->student_name,
                'adm_no' => $this->adm_no,
                'stream_id' => $streamIdValue,
                'form' => $formValue,
                'form_sequence_number' => $formSequenceNumber,  
            ]);

        // Reset the input fields and show a success message
        $this->resetInputFields();

        session()->flash('message', 'Student added successfully.');

        $this->dispatch('storeStudentDetails', [
            'student_name' => $this->student_name,
            'adm_no' => $this->adm_no,
            'class' => $this->class,
            'stream_id' => $streamIdValue,
        ]);
    }

    public function resetInputFields()
    {
        $this->student_name = '';
        $this->adm_no = '';
        $this->class = '';
        $this->stream_id = '';
        $this->term ='';
    }
}; ?>

<div>
    <div class="container mt-5">
        <h2 class="mb-4 text-2xl font-bold">Add Student Details</h2>
    
        <form wire:submit.prevent="submit" class="needs-validation" novalidate>
            <div class="mb-3">
                <input type="text" class="form-input" wire:model="student_name" placeholder="Student Name" required>
                @error('student_name') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <input type="text" class="form-input" wire:model="adm_no" placeholder="Admission Number" required>
                @error('adm_no') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <select class="form-select" wire:model="class" required>
                    <option value="">Select Class</option>
                    @foreach ($class_forms as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                @error('class') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
            </div>
    
            @if($streams->isNotEmpty())
                <div class="mb-3">
                    <select class="form-select" wire:model="stream_id" required>
                        <option value="">Select Stream</option>
                        @foreach ($streams as $stream)
                            <option value="{{ $stream->id }}">{{ $stream->name }}</option>
                        @endforeach
                    </select>
                    @error('stream_id') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                </div>
            @endif
    
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
