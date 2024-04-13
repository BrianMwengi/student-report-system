<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;

new class extends Component {

    #[Validate('required|string|max:255')]
    public $name;

    public function submit(): void
    {
        // Validation rules
        $validatedData = $this->validate();

        // Create a new class
        ClassForm::create($validatedData);

        // Show a success message or redirect to another page
        session()->flash('message', 'Class added successfully.');
    }

}; ?>

<div>
    <div>
        <h2 class="text-xl font-bold">Add Class</h2>
    
        <form wire:submit.prevent="submit" class="mt-4">
            <input type="text" id="name" class="form-input @error('name') border-red-500 @enderror" wire:model="name" placeholder="Class Name">
    
            @error('name')
                <div class="text-red-500 mt-1">
                    {{ $message }}
                </div>
            @enderror
    
            <button type="submit" class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Submit</button>
        </form>
    
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="mt-4 bg-success-500 text-white font-bold py-2 px-4 rounded">
                {{ session('message') }}
            </div>
        @endif
    
        <!-- Display error message if any -->
        @if (session()->has('error'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" class="mt-4 bg-error-500 text-white font-bold py-2 px-4 rounded">
                {{ session('error') }}
            </div>
        @endif
    </div>    
</div>
