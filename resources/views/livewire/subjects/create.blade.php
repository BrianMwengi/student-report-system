<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Subject;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $name = '';

    public function submit(): void
    {
        $validatedData = $this->validate();

        // Perform validation and save the data to the database
        Subject::create($validatedData);

        // Show a success message or redirect to another page
        $this->dispatch('success', message: "Subject added successfully!");

        // Reset the input field
        $this->name = '';
    }
}; ?>

<div>
    <div class="max-w-lg mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="col-span-1 md:col-span-4">
                <h2 class="text-xl font-bold">Add Subject</h2>
    
                <form wire:submit = "submit" class="mt-4">
                    <div class="form-group">
                        <input type="text" id="name" class="form-input @error('name') border-red-500 @enderror" wire:model="name" placeholder="Subject Name">
    
                        @error('name')
                            <div class="text-red-500 mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
    
                    <div class="form-group mt-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Save</button>
                    </div>
                </form>
                <div x-data="{ open: false, message: ''}" 
                @success.window="open = true; message=$event.detail.message; setTimeout(() => open = false, 4000)"
                x-show="open"
                x-text="message"
                class="mt-4 bg-green-500 text-white font-bold py-2 px-4 rounded">
            <span x-text="message"></span>
       </div>
            </div>
        </div>
    </div>
    
</div>

