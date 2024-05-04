<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Subject;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $name = '';

    public function submit(): void
    {
        // Convert the name to lowercase for a case-insensitive comparison
        $nameLowercase = strtolower($this->name);

        // Check if a subject with the same name (case-insensitive) already exists
        $subjectExists = Subject::whereRaw('lower(name) = ?', [$nameLowercase])->exists();

        if ($subjectExists) {
            // Flash a message to inform the user that the subject already exists
            $this->dispatch('success', message: "A subject with the same name already exists.");
        } else {
            // Perform validation
            $validatedData = $this->validate();

            // Since 'name' is validated, override it with the original input (case-sensitive)
            $validatedData['name'] = $this->name;

            // Save the data to the database
            Subject::create($validatedData);

            // Show a success message or redirect to another page
            $this->dispatch('success', message: "Subject added successfully!");

            // Reset the input field
            $this->name = '';
        }
    }
}; ?>


<div>
    <div class=" container mt-5 p-6 bg-white shadow-md rounded-lg max-w-lg mx-auto">
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
</div>
</div>
</div>    
</div>

