<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\SchoolSetting;

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
        $this->settings = SchoolSetting::first() ?? new SchoolSetting;
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
        $this->validate();
    
        if ($this->logo) {
            $logoPath = $this->logo->store('logos', 'public');
            $this->settings->logo_url = $logoPath;
        } else {
            // If there is no new logo (i.e., it's null), then use the existing logo URL instead
            $existingSettings = SchoolSetting::first();
            if ($existingSettings) {
                $this->settings->logo_url = $existingSettings->logo_url;
            }
        }
    
        // Save or update the settings
        if ($this->settings) {
            $this->settings->save();
        }
    
        session()->flash('message', 'Settings Updated Successfully.');
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
        <form wire:submit.prevent="saveSettings" class="needs-validation" novalidate>
            {{-- Display flash message --}}
    
            <div class="mb-3">
                <label for="logo_url" class="form-label">School Logo</label>
                <input wire:model="logo" type="file" id="logo_url" class="form-control">
                @error('logo') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <label for="school_name" class="form-label">School Name</label>
                <input wire:model="settings.school_name" type="text" id="school_name" class="form-control">
                @error('school_name') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <label for="current_year" class="form-label">Current Year</label>
                <input wire:model="settings.current_year" type="number" id="current_year" class="form-control">
                @error('current_year') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
    
            <div class="mb-3">
                <label for="term" class="form-label">Term</label>
                <input wire:model="settings.term" type="text" id="term" class="form-control">
                @error('term') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="term_start_date" class="form-label">Term Start Date</label>
                <input wire:model="settings.term_start_date" type="date" id="term_start_date" class="form-control">
                @error('term_start_date') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="term_end_date" class="form-label">Term End Date</label>
                <input wire:model="settings.term_end_date" type="date" id="term_end_date" class="form-control">
                @error('term_end_date') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="next_term_start_date" class="form-label">Next Term Start Date</label>
                <input wire:model="settings.next_term_start_date" type="date" id="next_term_start_date" class="form-control">
                @error('next_term_start_date') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="next_term_end_date" class="form-label">Next Term End Date</label>
                <input wire:model="settings.next_term_end_date" type="date" id="next_term_end_date" class="form-control">
                @error('next_term_end_date') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <label for="school_motto" class="form-label">School Motto</label>
                <input wire:model="settings.school_motto" type="text" id="school_motto" class="form-control">
                @error('school_motto') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label for="school_vision" class="form-label">School Vision</label>
                <input wire:model="settings.school_vision" type="text" id="school_vision" class="form-control">
                @error('school_vision') <div class="alert alert-danger">{{ $message }}</div> @enderror
            </div>
            
            <div class="mb-3">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Save Settings
                </button>
            </div>
        </form>
    </div>    
</div>
