<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\SchoolSettings;

new class extends Component {
    use WithFileUploads;
    
    #[Validate('image|max:1024')]
    public $logo;
    #[Validate('required')]
    public $school_name;
    #[Validate('required|integer')]
    public $current_year;
    #[Validate('required|string')]
    public $term;
    #[Validate('required|date')]
    public $term_start_date;
    #[Validate('required|date')]
    public $term_end_date;
    #[Validate('required|date')]
    public $next_term_start_date;
    #[Validate('required|date')]
    public $next_term_end_date;
    #[Validate('required|string')]
    public $school_motto;
    #[Validate('required|string')]
    public $school_vision;
    public $settings;


    public function mount()
    {
        $this->settings = SchoolSettings::first() ?? new SchoolSettings;
    }

    public function updatedSettingsLogoUrl()
    {
        if ($this->settings) {
            $this->validate([
                'settings.logo_url' => 'image', // Validate here immediately upon upload
            ]);
    
            // Store the image and keep its path in the component
            $this->settings->logo_url = $this->settings->logo_url->store('logos', 'public');
        }
    }

   public function saveSettings()
   {
        // Validate the input data
        $this->validate();

        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $this->settings->logo_url = $logoPath;
        } else {
            // If there is no new logo (i.e., it's null), then use the existing logo URL instead
            $existingSettings = SchoolSettings::first();
            if ($existingSettings) {
                $this->settings->logo_url = $existingSettings->logo_url;
            }
        }

        // Set other required fields
        $this->settings->school_name = $this->school_name;
        $this->settings->current_year = $this->current_year;
        $this->settings->term = $this->term;
        $this->settings->term_start_date = $this->term_start_date;
        $this->settings->term_end_date = $this->term_end_date;
        $this->settings->next_term_start_date = $this->next_term_start_date;
        $this->settings->next_term_end_date = $this->next_term_end_date;
        $this->settings->school_motto = $this->school_motto;
        $this->settings->school_vision = $this->school_vision;

        // Save or update the settings
        if ($this->settings) {
            $this->settings->save();
        }
    
        $this->dispatch('success', message: "Exam details added successfully!");
    }

    public function with(): array
    {
        return [
            'settings' => $this->settings,
        ];
    }
}; ?>

<div>
    <div>
        <form wire:submit="saveSettings" class="needs-validation" novalidate>
            {{-- Display flash message --}}
            <div x-data="{ open: false, message: '' }" 
                x-cloak
                @success.window="open = true; message = $event.detail.message; setTimeout(() => open = false, 4000)"
                x-show="open"
                class="mt-4 bg-green-500 text-white font-bold py-2 px-4 rounded">
                <span x-text="message"></span>
            </div>
    
            <div class="mb-4">
                <label for="logo_url" class="block text-sm font-medium text-gray-700">School Logo</label>
                <input wire:model="logo" type="file" id="logo_url" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('logo') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="school_name" class="block text-sm font-medium text-gray-700">School Name</label>
                <input wire:model="school_name" type="text" id="school_name" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('school_name') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="current_year" class="block text-sm font-medium text-gray-700">Current Year</label>
                <input wire:model="current_year" type="number" id="current_year" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('current_year') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="term" class="block text-sm font-medium text-gray-700">Term</label>
                <input wire:model="term" type="text" id="term" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('term') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="term_start_date" class="block text-sm font-medium text-gray-700">Term Start Date</label>
                <input wire:model="term_start_date" type="date" id="term_start_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('term_start_date') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="term_end_date" class="block text-sm font-medium text-gray-700">Term End Date</label>
                <input wire:model="term_end_date" type="date" id="term_end_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('term_end_date') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="next_term_start_date" class="block text-sm font-medium text-gray-700">Next Term Start Date</label>
                <input wire:model="next_term_start_date" type="date" id="next_term_start_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('next_term_start_date') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="next_term_end_date" class="block text-sm font-medium text-gray-700">Next Term End Date</label>
                <input wire:model="next_term_end_date" type="date" id="next_term_end_date" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('next_term_end_date') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="school_motto" class="block text-sm font-medium text-gray-700">School Motto</label>
                <input wire:model="school_motto" type="text" id="school_motto" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('school_motto') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <label for="school_vision" class="block text-sm font-medium text-gray-700">School Vision</label>
                <input wire:model="school_vision" type="text" id="school_vision" class="mt-1 block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                @error('school_vision') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Save Settings
                </button>
            </div>
        </form>
    </div>    
    </div>    
</div>
