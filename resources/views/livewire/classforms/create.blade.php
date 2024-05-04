<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\ClassForm;

new class extends Component {

    #[Validate('required|string|max:255')]
    public $name = '';

    public function submit(): void
    {
        // Validation rules
        $validatedData = $this->validate();

        // Create a new class
        ClassForm::create($validatedData);

        // Show a success message or redirect to another page
        $this->dispatch('success', message: "Class added successfully");

         // Reset the 'name' property
         $this->name = '';
    }

}; ?>

<div>
    <div>
        <h2 class="text-xl font-bold">Add Class</h2>
    
        <form wire:submit="submit" class="mt-4">
            <input type="text" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" wire:model="name" placeholder="Class Name">
           
            @error('name')
                <div class="text-red-500 mt-1">
                    {{ $message }}
                </div>
            @enderror
    
            <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
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
</div>

