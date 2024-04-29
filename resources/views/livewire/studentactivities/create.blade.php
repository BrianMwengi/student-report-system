<?php

use Livewire\Volt\Component;
use App\Models\ClassForm;
use App\Models\Student;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

new class extends Component {
    public $students;
    public $classforms;
    public $forms;
    public $selectedStudent = null;
    public $selectedClass = null;
    #[Validate('required|string|max:255')]
    public $responsibilities = '';
    #[Validate('required|string|max:255')]
    public $clubs = '';
    #[Validate('required|string|max:255')]
    public $sports = '';
    #[Validate('required|string|max:255')]
    public $house_comment = '';
    public $teacher_comment = '';
    public $principal_comment = '';
    public $activityId;

    // Load the initial data
    public function mount()
    {
        // Get all the class forms
        $this->classforms = ClassForm::all();
        // Get all the forms available
        $this->forms = Student::select('form')->distinct()->get();
        // Start with an empty collection of students
        $this->students = collect(); 
    }
   
    public function updatedSelectedClass()
    {
       // Get all the students in the selected form.
       $this->students = Student::where('form', $this->selectedClass)->get();
        // Reset the selected student when changing form.
        $this->selectedStudent = null; 
    }
    
    public function saveComments()
    {
        // Validate the input data
        $this->validate();

        $student = Student::find($this->selectedStudent);
    
        // Get existing activity or create a new one
        $activity = $student->activity()->firstOrNew(['student_id' => $student->id]);
    
        // Only update the fields if they are provided
        $activity->responsibilities = $this->responsibilities ?? $activity->responsibilities;
        $activity->clubs = $this->clubs ?? $activity->clubs;
        $activity->sports = $this->sports ?? $activity->sports;
        $activity->house_comment = $this->house_comment ?? $activity->house_comment;
    
        // Check if teacher_comment is provided before saving
        if ($this->teacher_comment) {
            $activity->teacher_comment = $this->teacher_comment;
        }
    
        // Check if principal_comment is provided before saving
        if ($this->principal_comment) {
            $activity->principal_comment = $this->principal_comment;
        }
    
        // Save the activity
        $activity->save();
    
        // Reset the form fields
        $this->responsibilities = '';
        $this->clubs = '';
        $this->sports = '';
        $this->house_comment = '';
        $this->teacher_comment = '';
        $this->principal_comment = '';
    }
   

    public function updatedSelectedStudent()
    {
        // Get the current activities of the selected student.
        $activity = Student::find($this->selectedStudent)->activity;
        
        // If there is a student activity, load it into your local variables.
        if($activity) {
            $this->responsibilities = $activity->responsibilities;
            $this->clubs = $activity->clubs;
            $this->sports = $activity->sports;
            $this->house_comment = $activity->house_comment;
            $this->teacher_comment = $activity->teacher_comment;
            $this->principal_comment = $activity->principal_comment;
        } else {
            // If there is no student activity, reset the local variables.
            $this->responsibilities = '';
            $this->clubs = '';
            $this->sports = '';
            $this->house_comment = '';
            $this->teacher_comment = '';
            $this->principal_comment = '';
        }
    }
    
    // Return the view to render
    public function with(): array
    {
        // Get all the forms
        $this->forms = Student::select('form')->distinct()->get();
        // Get all the students in the selected form.
        if ($this->selectedClass) {
            $this->students = Student::where('form', $this->selectedClass)->get();
        }

        // Return the data as an array
        return [ 
            'forms' => $this->forms,
            'students' => $this->students,
        ];
    }
}; ?>

<div wire:poll.500ms>
    <div class="container">
        <div class="grid grid-cols-1 gap-3">
            <!-- Select dropdown for choosing a class -->
            <div>
                <label for="selectedClass" class="block text-sm font-medium text-gray-700">Select a Form:</label>
                <select id="selectedClass"  wire:model="selectedClass" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">Select a ClassForm</option>
                    @foreach($forms as $form)
                        <option value="{{ $form->form }}">{{ 'Form ' . $form->form }}</option>
                    @endforeach
                </select>
            </div>
    
            <!-- Select dropdown for choosing a student -->
            @if(! $students->isEmpty())
                <div>
                    <label for="selectedStudent" class="block text-sm font-medium text-gray-700">Select a Student:</label>
                    <select id="selectedStudent" wire:model="selectedStudent" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Select a Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name }} (Adm No: {{ $student->adm_no }})</option>
                        @endforeach
                    </select>
                </div>
            @endif
    
            <!-- The form to enter data -->
            @if($selectedStudent)
                <form wire:submit="saveComments">
                    <!-- Input fields for comments -->
                    <div class="mb-3">
                        <label for="responsibilities" class="block text-sm font-medium text-gray-700">Responsibilities:</label>
                        <textarea id="responsibilities" wire:model="responsibilities" placeholder="Responsibilities" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        @error('responsibilities') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="clubs" class="block text-sm font-medium text-gray-700">Clubs:</label>
                        <textarea id="clubs" wire:model="clubs" placeholder="Clubs" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                        @error('clubs') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label for="sports" class="block text-sm font-medium text-gray-700">Sports:</label>
                        <textarea id="sports" wire:model="sports" placeholder="Sports" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:border-blue-300"></textarea>
                        @error('sports') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="house_comment" class="block text-sm font-medium text-gray-700">House Comment:</label>
                        <textarea id="house_comment" wire:model="house_comment" placeholder="House comment" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:border-blue-300"></textarea>
                        @error('house_comment') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="teacher_comment" class="block text-sm font-medium text-gray-700">Teacher's Comment:</label>
                        @if ($teacher_comment)
                            <textarea id="teacher_comment" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>Comment already provided</textarea>
                        @else
                            <textarea id="teacher_comment" wire:model.debounce.4000ms="teacher_comment" placeholder="Teacher's comment" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:border-blue-300"></textarea>
                        @endif
                        @error('teacher_comment') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="principal_comment" class="block text-sm font-medium text-gray-700">Principal's Comment:</label>
                        @if ($principal_comment)
                            <textarea id="principal_comment" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm bg-gray-100" readonly>Comment already provided</textarea>
                        @else
                            <textarea id="principal_comment" wire:model.debounce.4000ms="principal_comment" placeholder="Principal's comment" class="mt-1 p-2 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:border-blue-300"></textarea>
                        @endif
                        @error('principal_comment') <div class="text-red-500 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:ring-opacity-50 focus:ring-blue-300">Submit</button>
                </form>
            @endif
        </div>
    </div>
    
</div>
