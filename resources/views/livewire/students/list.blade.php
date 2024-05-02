<?php

use Livewire\Volt\Component;
use App\Models\Student;
use Livewire\Attributes\On; 
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $form = 1;
    public $searchTerm = '';
    public $sortColumn = 'name';
    public $sortDirection = 'asc';

    public function updatedForm($value)
    {
        session()->put('students_list_form', $value);
        $this->resetPage();
        $this->students = $this->getStudents();
    }

    public function sortBy($column)
    {
        $this->sortColumn = $column;
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
    }

    #[On('student-created')]
    public function getStudents()
    {
        return Student::where('form', $this->form)
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->with('stream')
            ->orderByRaw('CAST(form_sequence_number AS INT) ' . $this->sortDirection)
            ->paginate(10);
    }

    public function state(): array
    {
        return [
            'form' => session()->get('students_list_form', 1),
        ];
    }

    public function with(): array
    {
        return [
            'students' => $this->getStudents(),
        ];
    }
}; ?>
<div wire:poll.500ms>
    <div class="container p-6 bg-white shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-3">
            <div class="col-auto">
                <label for="form" class="block text-sm font-medium text-gray-700">Select Form:</label>
                <select wire:model="form" id="form" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:border-blue-300">
                    <option value="1">Form 1</option>
                    <option value="2">Form 2</option>
                    <option value="3">Form 3</option>
                    <option value="4">Form 4</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="text" wire:model="searchTerm" placeholder="Search students..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:border-blue-300">
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

    <div class="bg-white shadow overflow-x-auto sm:rounded-md">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission Number</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stream</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($students->items() as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $student->form_sequence_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $student->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $student->adm_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $student->stream ? $student->stream->name : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <button wire:click="confirmDelete()" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
                            <a href="" class="text-blue-600 hover:text-blue-900 ml-2">View Report Card</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

        <div class="mt-4">
            @if($students->total())
                @if($students->count())
                    {{ $students->links() }}
                @else
                    <p class="text-yellow-600">No student found with that name.</p>
                @endif
            @else
                <p class="text-yellow-600">No students added yet.</p>
            @endif
        </div>
    </div>