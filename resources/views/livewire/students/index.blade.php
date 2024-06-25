<?php

use Livewire\Volt\Component;
use App\Models\Student;

new class extends Component {
    public $students;

    public function mount()
    {
        $this->students = Student::with(['classForm', 'stream'])->get();
    }

    public function with(): array
    {
        return [
            'students' => $this->students,
        ];
    }
};
?>
<div class="overflow-x-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Admission Number</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Class</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stream</th>
                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($students as $student)
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-900">
                    <td class="py-4 px-6 text-sm font-medium text-gray-900 dark:text-gray-200">{{ $student->id }}</td>
                    <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-200">{{ $student->name }}</td>
                    <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-200">{{ $student->adm_no }}</td>
                    <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-200">{{ $student->classForm->name }}</td>
                    <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-200">{{ $student->stream->name }}</td>
                    <td class="py-4 px-6 text-sm text-gray-900 dark:text-gray-200">
                        <a href="{{ route('students.edit', $student->id) }}" class="text-indigo-600 hover:text-indigo-900 dark:hover:text-indigo-300">Edit</a>
                        <a href="{{ route('students.show', $student->id) }}" class="ml-4 text-blue-600 hover:text-blue-900 dark:hover:text-blue-300">View</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
