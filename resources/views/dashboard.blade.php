<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Dashboard Content -->
                <div class="container mx-auto p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Summary Cards -->
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold">Total Students</h3>
                            <p class="text-2xl">{{ $totalStudents }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold">Total Teachers</h3>
                            <p class="text-2xl">{{ $totalTeachers }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold">Total Classes</h3>
                            <p class="text-2xl">{{ $totalClasses }}</p>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <h3 class="text-xl font-bold">Total Subjects</h3>
                            <p class="text-2xl">{{ $totalSubjects }}</p>
                        </div>
                    </div>

                    <!-- Charts and Graphs -->
                    <div class="mt-6">
                        <h3 class="text-xl font-bold">Students per Class</h3>
                        <div id="students-per-class-chart" class="h-64"></div>
                    </div>
                    <div class="mt-6">
                        <h3 class="text-xl font-bold">Average Scores by Subject</h3>
                        <div id="average-scores-chart" class="h-64"></div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="mt-6">
                        <h3 class="text-xl font-bold">Recent Exam Entries</h3>
                        <ul>
                            @foreach ($recentExams as $exam)
                                <li>{{ $exam->student->name }} - {{ $exam->subject->name }}: {{ $exam->average_score }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="mt-6">
                        <h3 class="text-xl font-bold">Recent Student Registrations</h3>
                        <ul>
                            @foreach ($recentStudents as $student)
                                <li>{{ $student->name }} - {{ $student->created_at->format('d M Y') }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Quick Links -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <a href="{{ route('students.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New Student</a>
                        <a href="{{ route('exams.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New Exam</a>
                        <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">View All Students</a>
                        <a href="{{ route('exams.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">View All Exams</a>
                    </div>

                    <!-- Announcements or Notices -->
                    <div class="mt-6 bg-white p-4 rounded-lg shadow-md">
                        <h3 class="text-xl font-bold">Important Notices</h3>
                        <ul>
                            @foreach ($notices as $notice)
                                <li>{{ $notice->message }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Add necessary JavaScript for charts (e.g., Chart.js) -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script>
                    // Example script to initialize a chart
                    var ctx = document.getElementById('students-per-class-chart').getContext('2d');
                    var chart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: {!! json_encode($classNames) !!},
                            datasets: [{
                                label: 'Students per Class',
                                data: {!! json_encode($studentCounts) !!},
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                </script>
            </div>
        </div>
    </div>
</x-app-layout>
