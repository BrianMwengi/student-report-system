<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\Exam;
use Livewire\Attributes\On; 
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    // These are the public properties that will be accessible in the blade file
    public $student_id;
    public $form = 1;
    public $searchTerm = '';
    public $sortColumn = 'name';
    public $sortDirection = 'asc';
    public ?Student $editing = null;
    public $selectedStudentId = null;

    // updateForm is called when the form select is changed
    public function updatedForm($value)
    {   
       // Save the selected form to the session
        session()->put('students_list_form', $value);
        // Reset the page to 1
        $this->resetPage();
        // Get the students for the selected form
        $this->students = $this->getStudents();
    }

    // Sort the students by the selected column
    public function sortBy($column)
    {
        // If the column is already sorted, reverse the sort direction
        $this->sortColumn = $column;
        // Reverse the sort direction
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    // Listen for the student-deleted event
    #[On('studentDeleted')]
    public function studentDeleted($message)
    {
        session()->flash('message', $message);
        $this->render();
    }
    
    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', ['id' => $id]);
    }

    #[On('deleteStudent')]
    public function deleteStudent($id)
    {
        $this->student_id = $id;

        // Find and delete the related exam records
        Exam::where('student_id', $this->student_id)->delete();

        // Find the student
        $student = Student::find($this->student_id);

        // Check if the student exists and delete
        if ($student) {
            $student->delete();
        }

        // Dispatch an event to refresh the student list and pass the message
        $this->dispatch('studentDeleted', 'Student deleted successfully.');
    }

    #[On('student-created')]
    public function getStudents()
    {
        // Get the students for the selected form
        return Student::where('form', $this->form)
        // Search for students with the search term
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            // Eager load the stream relationship
            ->with('stream')
            // Order the students by the form sequence number
            ->orderByRaw('CAST(form_sequence_number AS INT) ' . $this->sortDirection)
            ->paginate(10);
    }

    public function openModal($id)
    {
        $this->selectedStudentId = $id;
    }

    public function closeModal()
    {
        $this->selectedStudentId = null;
    }

    public function edit(Student $student): void
    {
        $this->editing = $student;
        $this->getStudents();
        $this->render();
    }

    #[On('student-updated')]
    public function disableEditing()
    {
        $this->editing = null;

        $this->getStudents();
    }

    // State method to persist the form selection
    public function state(): array
    {
        return [
            // Get the form from the session
            'form' => session()->get('students_list_form', 1),
        ];
    }

    // Return the students and the form
    public function with(): array
    {
        return [
            // Get the students for the selected form
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

        {{--Flash message --}}
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md my-4" role="alert">
                {{ session('message') }}
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
                <div>
                    <table>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students->items() as $student)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->form_sequence_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->adm_no }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $student->stream->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <button wire:click="openModal({{ $student->id }})" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</button>
                                        <button wire:click="confirmDelete({{ $student->id }})" class="text-red-600 hover:text-red-900 ml-2">Delete</button>
                                        <a href="" class="text-blue-600 hover:text-blue-900 ml-2">View Report Card</a>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                @if($selectedStudentId === $student->id)
                                    <div class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                                            <!-- Modal content -->
                                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    @livewire('students.edit', ['id' => $student->id])
                                                </div>
                                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                    <button wire:click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

    <div class="mt-4">
        @if($students->count())
            {{ $students->links() }}
        @else
            @if($searchTerm)
                <p class="text-yellow-600">No student found with that name.</p>
            @else
                <p class="text-yellow-600">No students added yet.</p>
            @endif
        @endif
    </div>
</div>

@script
<script>
    window.addEventListener('show-delete-confirmation', function(event) {
        let id = event.detail[0]; // Access the student's ID
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.dispatchSelf('deleteStudent', id);
            }
        });
    });
</script>
@endscript
