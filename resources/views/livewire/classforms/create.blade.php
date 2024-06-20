<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\ClassForm;

new class extends Component {
    public $name = '';

    public function submit(): void
    {
        // Validate the class name format first
        $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^Form\s[1-4]$/i', $value)) {
                        $fail('The ' . $attribute . ' must be in the format "Form 1", "Form 2", "Form 3", or "Form 4".');
                    }
                },
            ],
        ]);

        // Convert the input name to lowercase for a case-insensitive comparison
        $nameLowercase = strtolower($this->name);

        // Then, check if a class with the same name (case-insensitive) already exists
        $classExists = ClassForm::whereRaw('LOWER(name) = ?', [$nameLowercase])->exists();

        if ($classExists) {
            // If a class with the same name exists, use Livewire's error bag to add an error
            session()->flash('error', 'A class with this name already exists.');
            return; // Stop execution to prevent creating the class
        }

        // Proceed to create a new class with the validated data
        ClassForm::create(['name' => $this->name]);

        // Show a success message or redirect to another page
        $this->dispatch('success', message: "Class added successfully");

        // Reset the 'name' property
        $this->name = '';
    }
}; ?>
<div>
    <div class="container mt-5 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-xl font-bold">Add Class</h2>
    
        <form wire:submit.prevent="submit" class="mt-4">
            <input type="text" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" wire:model="name" placeholder="Class Name">
        
            @error('name')
                <div class="text-red-500 mt-1">
                    {{ $message }}
                </div>
            @enderror
    
            <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
        </form>

        {{-- Flash success message --}}
        <div x-data="{ open: false, message: '' }" 
             x-cloak
                @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 4000)"
                x-show="open"
                class="mt-4 bg-green-500 text-white font-bold py-2 px-4 rounded">
               <span x-text="message"></span>
        </div>
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
    </div>    
</div>

