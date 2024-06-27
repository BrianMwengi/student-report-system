<?php

namespace tests\Feature;

use Tests\TestCase;
use App\Models\Stream;
use Livewire\Livewire;
use App\Models\Student;
use App\Models\ClassForm;
use Livewire\Volt\Component;
use GuzzleHttp\Promise\Create;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateStudentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_student_with_valid_data()
    {
        // Create necessary data for the test
        $classForm = ClassForm::factory()->create(); // Create a ClassForm instance
        $stream = Stream::factory()->create(['class_id' => $classForm->id]); // Create a Stream instance with the created ClassForm

        // Simulate the Livewire component
        Livewire::test('students.create')
            ->set('student_name', 'John Doe')
            ->set('adm_no', '12345')
            ->set('class', $classForm->id) // assuming class form ID
            ->set('stream_id', $stream->id) // assuming stream ID
            ->call('submit')
            ->assertHasNoErrors();

        // Assert the student was created
        $this->assertTrue(Student::where('adm_no', '12345')->exists());
    }

    /** @test */
    public function it_shows_validation_errors_with_invalid_data()
    {
        Livewire::test('students.create')
            ->set('student_name', '')
            ->set('adm_no', '')
            ->set('class', null)
            ->set('stream_id', null)
            ->call('submit')
            ->assertHasErrors(['student_name', 'adm_no', 'class', 'stream_id']);
    }

    /** @test */
    public function it_validates_unique_admission_number()
    {
        // Create existing student
        $student = Student::factory()->create(['adm_no' => '12345']);

        Livewire::test('students.create')
            ->set('student_name', 'Jane Doe')
            ->set('adm_no', '12345') // Duplicate admission number
            ->call('submit')
            ->assertHasErrors(['adm_no' => 'unique']);
    }

    /** @test */
    public function it_validates_stream_and_form_mismatch()
    {
        // Create data with mismatched stream and form
        $classForm = ClassForm::factory()->create(['name' => 'Form 2']);
        $stream = Stream::factory()->create(['name' => 'Form 1A', 'class_id' => $classForm->id]);

        Livewire::test('students.create')
            ->set('student_name', 'Jane Doe')
            ->set('adm_no', '67890')
            ->set('class', $classForm->id) // Form 2
            ->set('stream_id', $stream->id) // Stream with mismatched form
            ->call('submit')
            ->assertHasErrors(['stream_id']);
    }
}
