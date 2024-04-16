<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Stream;

new class extends Component {
    #[Validate('required|string|max:255')]
    public $name = '';
    #[Validate('required|exists:classes,id')]
    public $class_id ='';

    public function submit()
    {
        // Validation rules
        $validatedData = $this->validate();

        // Create a new stream
        Stream::create($validatedData);

        // Show a success message or redirect to another page
        $this->dispatch('success', message: "Stream added successfully!");
    }
}; ?>

<div>
    <div>
        <h2 class="text-xl font-bold">Add Stream</h2>
    
        <form wire:submit ="submit" class="mt-4">
            <div class="form-group">
                <input type="text" class="form-input @error('name') border-red-500 @enderror" wire:model="name" placeholder="Stream Name">
    
                @error('name')
                <div class="text-red-500 mt-1">
                    {{ $message }}
                </div>
                @enderror
            </div>
    
            <div class="form-group mt-4">
                <select class="form-select @error('class_id') border-red-500 @enderror" wire:model="class_id">
                    <option value="">Select Class</option>
                    @foreach ($classes as $class)
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
    </div>
    
</div>
