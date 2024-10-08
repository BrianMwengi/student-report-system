<?php

use Livewire\Volt\Component;
use App\Models\Student;
use App\Models\ClassForm;
use App\Models\Subject;
use App\Models\Exam;

new class extends Component {
    public $totalStudents;
    public $totalTeachers;
    public $totalClasses;
    public $totalSubjects;
    public $recentExams;
    public $recentStudents;
    public $studentsPerClass = [];
    public $averageScoresBySubject = [];

    // The mount method is called when the component is initialized
    public function mount()
    {
        // Count total number of students
        $this->totalStudents = Student::count();

        // Count unique teachers based on the teacher field in exams table
        $this->totalTeachers = Exam::distinct('teacher')->count('teacher');

        // Count total number of classes
        $this->totalClasses = ClassForm::count();

        // Count total number of subjects
        $this->totalSubjects = Subject::count();

        // Get the latest 5 exam entries
        $this->recentExams = Exam::latest()->take(5)->get();

        // Get the latest 5 student registrations
        $this->recentStudents = Student::latest()->take(5)->get();

        // Fetch the number of students per class
        $this->studentsPerClass = Student::select('form', \DB::raw('count(*) as total'))
            ->groupBy('form')
            ->pluck('total', 'form')
            ->toArray();

        // Fetch average scores by subject
        $this->averageScoresBySubject = Exam::select('subject_id', \DB::raw('avg(average) as avg_score'))
            ->groupBy('subject_id')
            ->pluck('avg_score', 'subject_id')
            ->toArray();
    }

    // The with method returns an array of variables to pass to the view
    public function with(): array
    {
        return [
            'totalStudents' => $this->totalStudents,
            'totalTeachers' => $this->totalTeachers,
            'totalClasses' => $this->totalClasses,
            'totalSubjects' => $this->totalSubjects,
            'recentExams' => $this->recentExams,
            'recentStudents' => $this->recentStudents,
            'studentsPerClass' => $this->studentsPerClass,
            'averageScoresBySubject' => $this->averageScoresBySubject,
        ];
    }
};?>
<div x-data="dashboardData({{ json_encode($studentsPerClass) }}, {{ json_encode($averageScoresBySubject) }})" x-init="initCharts()">
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
            <canvas id="students-per-class-chart" class="h-64"></canvas>
        </div>
        <div class="mt-6">
            <h3 class="text-xl font-bold">Average Scores by Subject</h3>
            <canvas id="average-scores-chart" class="h-64"></canvas>
        </div>

        <!-- Recent Activities -->
        <div class="mt-6">
            <h3 class="text-xl font-bold">Recent Exam Entries</h3>
            <ul>
                @foreach ($recentExams as $exam)
                    <li>{{ $exam->student->name }} - {{ $exam->subject->name }}: {{ $exam->average }}</li>
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
            <a href="{{ route('students') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New Student</a>
            <a href="{{ route('exams') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Add New Exam</a>
        </div>

        <!-- Announcements or Notices -->
        <div class="mt-6 bg-white p-4 rounded-lg shadow-md">
            <h3 class="text-xl font-bold">Important Notices</h3>
            <ul>
                <!-- Add your notices here -->
            </ul>
        </div>
    </div>

    <!-- Load Chart.js library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Alpine.js component for handling dashboard data and charts
        function dashboardData(studentsPerClass, averageScoresBySubject) {
            return {
                // Initialize data properties
                studentsPerClass,
                averageScoresBySubject,

                // Method to initialize charts
                initCharts() {
                    // Students per Class Chart
                    const studentsPerClassCtx = document.getElementById('students-per-class-chart').getContext('2d');
                    new Chart(studentsPerClassCtx, {
                        type: 'bar', // Set chart type to bar
                        data: {
                            labels: Object.keys(this.studentsPerClass), // Set x-axis labels
                            datasets: [{
                                label: 'Students per Class', // Set dataset label
                                data: Object.values(this.studentsPerClass), // Set data points
                                backgroundColor: 'rgba(54, 162, 235, 0.2)', // Set background color
                                borderColor: 'rgba(54, 162, 235, 1)', // Set border color
                                borderWidth: 1 // Set border width
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true // Start y-axis at zero
                                }
                            }
                        }
                    });

                    // Average Scores by Subject Chart
                    const averageScoresCtx = document.getElementById('average-scores-chart').getContext('2d');
                    new Chart(averageScoresCtx, {
                        type: 'bar', // Set chart type to bar
                        data: {
                            labels: Object.keys(this.averageScoresBySubject).map(id => `Subject ${id}`), // Set x-axis labels
                            datasets: [{
                                label: 'Average Scores by Subject', // Set dataset label
                                data: Object.values(this.averageScoresBySubject), // Set data points
                                backgroundColor: 'rgba(75, 192, 192, 0.2)', // Set background color
                                borderColor: 'rgba(75, 192, 192, 1)', // Set border color
                                borderWidth: 1 // Set border width
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true // Start y-axis at zero
                                }
                            }
                        }
                    });
                }
            };
        }
    </script>
</div>


