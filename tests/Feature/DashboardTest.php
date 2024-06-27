<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Dashboard;
use App\Models\Student;
use App\Models\ClassForm;
use App\Models\Subject;
use App\Models\Exam;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_total_students_correctly()
    {
        Student::factory()->count(10)->create();

        Livewire::test('dashboard.create')
            ->assertSet('totalStudents', 10);
    }

    /** @test */
    public function it_calculates_total_teachers_correctly()
    {
        Exam::factory()->count(5)->create(['teacher' => 'Teacher 1']);
        Exam::factory()->count(5)->create(['teacher' => 'Teacher 2']);

        Livewire::test('dashboard.create')
            ->assertSet('totalTeachers', 2);
    }

    /** @test */
    public function it_calculates_total_classes_correctly()
    {
        ClassForm::factory()->count(5)->create();

        Livewire::test('dashboard.create')
            ->assertSet('totalClasses', 5);
    }

    /** @test */
    public function it_calculates_total_subjects_correctly()
    {
        Subject::factory()->count(5)->create();

        Livewire::test('dashboard.create')
            ->assertSet('totalSubjects', 5);
    }

    /** @test */
    public function it_displays_recent_exams_correctly()
    {
        $exams = Exam::factory()->count(5)->create();

        Livewire::test('dashboard.create')
            ->assertSee($exams->first()->student->name)
            ->assertSee($exams->first()->subject->name);
    }

    /** @test */
    public function it_displays_recent_students_correctly()
    {
        $students = Student::factory()->count(5)->create();

        Livewire::test('dashboard.create')
            ->assertSee($students->first()->name);
    }

    /** @test */
    public function it_calculates_students_per_class_correctly()
    {
        Student::factory()->count(5)->create(['form' => '1']);
        Student::factory()->count(3)->create(['form' => '2']);

        Livewire::test('dashboard.create')
            ->assertSet('studentsPerClass.1', 5)
            ->assertSet('studentsPerClass.2', 3);
    }

    /** @test */
    public function it_calculates_average_scores_by_subject_correctly()
    {
        Exam::factory()->create(['subject_id' => 1, 'average' => 80]);
        Exam::factory()->create(['subject_id' => 1, 'average' => 90]);
        Exam::factory()->create(['subject_id' => 2, 'average' => 70]);

        Livewire::test('dashboard.create')
            ->assertSet('averageScoresBySubject.1', 85)
            ->assertSet('averageScoresBySubject.2', 70);
    }
}
