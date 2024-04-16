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
    //
</div>
