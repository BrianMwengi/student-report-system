<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full bg-white dark:bg-gray-800">
                        <thead>
                            <tr>
                                <th class="py-2">ID</th>
                                <th class="py-2">Student Name</th>
                                <th class="py-2">Subject</th>
                                <th class="py-2">Exam 1</th>
                                <th class="py-2">Exam 2</th>
                                <th class="py-2">Exam 3</th>
                                <th class="py-2">Average</th>
                                <th class="py-2">Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($exams as $exam)
                                <tr>
                                    <td class="py-2">{{ $exam->id }}</td>
                                    <td class="py-2">{{ $exam->student->name }}</td>
                                    <td class="py-2">{{ $exam->subject->name }}</td>
                                    <td class="py-2">{{ $exam->exam1 }}</td>
                                    <td class="py-2">{{ $exam->exam2 }}</td>
                                    <td class="py-2">{{ $exam->exam3 }}</td>
                                    <td class="py-2">{{ $exam->average }}</td>
                                    <td class="py-2">{{ $exam->grade }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
