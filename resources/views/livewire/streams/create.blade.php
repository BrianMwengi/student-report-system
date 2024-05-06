<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Stream;
use App\Models\ClassForm;

new class extends Component {

    public $name = '';
    public $class_id = '';

    public function submit(): void
    {
        // Custom validation rule to ensure the name follows the specific format
        $validatedData = $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:streams,name',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^Form \d+[a-zA-Z\s]+$/', $value)) {
                        $fail('The ' . $attribute . ' must start with "Form ", followed by a number, and followed by one or more letters.');
                    }
                },
            ],
            'class_id' => ['required', 'exists:class_forms,id'],
        ]);

        // Create a new stream with the validated data
        Stream::create($validatedData);

       // Show a success message or redirect to another page
        $this->dispatch('success', message: "Class Stream added successfully");

        // Reset the form fields
        $this->name = '';
        $this->class_id = '';
    }


    // Load the class forms data 
    public function with(): array
    {
        $class_forms = ClassForm::all();

        return [ 
            'class_forms' => $class_forms,
        ];
    }
}; ?>

<div>
    <div class="container mt-5 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-xl font-bold">Add Stream</h2>
    
        <form wire:submit ="submit" class="mt-4">
            <div class="form-group">
                <input type="text" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" wire:model="name" placeholder="Stream Name">
    
                @error('name')
                <div class="text-red-500 mt-1">
                    {{ $message }}
                </div>
                @enderror
            </div>
    
            <div class="form-group mt-4">
                <select class="form-select block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('class_id') border-red-500 @enderror" wire:model="class_id">
                    <option value="">Select Class</option>
                    @foreach ($class_forms as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
    
                @error('class_id')
                <div class="text-red-500 mt-1">
                    {{ $message }}
                </div>
                @enderror
            </div>
    
            <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
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
