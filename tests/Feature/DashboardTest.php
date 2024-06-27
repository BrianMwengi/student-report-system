<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Stream;
use App\Models\Student;
use App\Models\ClassForm;
use App\Models\Subject;
use App\Models\Exam;
use Livewire\Livewire;
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
        // Create necessary data
        $classForm = ClassForm::factory()->create(['name' => 'Form 1']);
        $stream = Stream::factory()->create(['class_id' => $classForm->id, 'name' => 'form 1A']);

        // Create Student instances associated with the ClassForm and Stream
        $student1 = Student::factory()->create(['form' => $classForm->id, 'stream_id' => $stream->id]);
        $student2 = Student::factory()->create(['form' => $classForm->id, 'stream_id' => $stream->id]);

        // Create Subject instances
        $subject1 = Subject::factory()->create();
        $subject2 = Subject::factory()->create();

        // Create Exam instances for the students with different teachers
        Exam::factory()->count(5)->create(['teacher' => 'Teacher 1', 'student_id' => $student1->id, 'subject_id' => $subject1->id]);
        Exam::factory()->count(5)->create(['teacher' => 'Teacher 2', 'student_id' => $student2->id, 'subject_id' => $subject2->id]);

        // Test that the total teachers count is 2
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
        Subject::factory()->count(8)->create();

        Livewire::test('dashboard.create')
            ->assertSet('totalSubjects', 8);
    }

    /** @test */
    public function it_displays_recent_exams_correctly()
    {
        // Create necessary data
        $classForm = ClassForm::factory()->create();
        $stream = Stream::factory()->create(['class_id' => $classForm->id]);
        $subject = Subject::factory()->create();

        $exams = Exam::factory()->count(5)->create([
            'student_id' => Student::factory()->create(['form' => $classForm->id, 'stream_id' => $stream->id]),
            'subject_id' => $subject->id,
        ]);

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
        // Create necessary data
        $classForm1 = ClassForm::factory()->create(['name' => 'Form 1']);
        $classForm2 = ClassForm::factory()->create(['name' => 'Form 2']);
        $stream1 = Stream::factory()->create(['class_id' => $classForm1->id]);
        $stream2 = Stream::factory()->create(['class_id' => $classForm2->id]);

        Student::factory()->count(5)->create(['form' => $classForm1->id, 'stream_id' => $stream1->id]);
        Student::factory()->count(3)->create(['form' => $classForm2->id, 'stream_id' => $stream2->id]);

        Livewire::test('dashboard.create')
            ->assertSet('studentsPerClass.' . $classForm1->id, 5)
            ->assertSet('studentsPerClass.' . $classForm2->id, 3);
    }

    /** @test */
    public function it_calculates_average_scores_by_subject_correctly()
    {
        // Create necessary data
        $classForm = ClassForm::factory()->create();
        $stream = Stream::factory()->create(['class_id' => $classForm->id]);
        $subject1 = Subject::factory()->create();
        $subject2 = Subject::factory()->create();

        $student = Student::factory()->create(['form' => $classForm->id, 'stream_id' => $stream->id]);

        Exam::factory()->create(['subject_id' => $subject1->id, 'average' => 80, 'student_id' => $student->id]);
        Exam::factory()->create(['subject_id' => $subject1->id, 'average' => 90, 'student_id' => $student->id]);
        Exam::factory()->create(['subject_id' => $subject2->id, 'average' => 70, 'student_id' => $student->id]);

        Livewire::test('dashboard.create')
            ->assertSet('averageScoresBySubject.' . $subject1->id, 85)
            ->assertSet('averageScoresBySubject.' . $subject2->id, 70);
    }
}
