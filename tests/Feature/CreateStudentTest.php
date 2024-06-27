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
        $stream = Stream::factory()->create();
        $classForm = ClassForm::factory()->create();

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

    // /** @test */
    // public function it_shows_validation_errors_with_invalid_data()
    // {
    //     Livewire::test(Create::class)
    //         ->set('student_name', '')
    //         ->set('adm_no', '')
    //         ->set('class', null)
    //         ->set('stream_id', null)
    //         ->call('submit')
    //         ->assertHasErrors(['student_name', 'adm_no', 'class', 'stream_id']);
    // }

    // /** @test */
    // public function it_validates_unique_admission_number()
    // {
    //     Student::create([
    //         'name' => 'Jane Doe',
    //         'adm_no' => '12345',
    //         'form' => 1,
    //         'stream_id' => 1,
    //         'form_sequence_number' => 1
    //     ]);

    //     Livewire::test(Create::class)
    //         ->set('student_name', 'John Doe')
    //         ->set('adm_no', '12345')
    //         ->set('class', 1)
    //         ->set('stream_id', 1)
    //         ->call('submit')
    //         ->assertHasErrors(['adm_no']);
    // }

    // /** @test */
    // public function it_validates_stream_and_form_mismatch()
    // {
    //     // Assuming Stream 2 does not match Form 1
    //     Livewire::test(Create::class)
    //         ->set('student_name', 'John Doe')
    //         ->set('adm_no', '12345')
    //         ->set('class', 1)
    //         ->set('stream_id', 2)
    //         ->call('submit')
    //         ->assertHasErrors(['stream_id']);
    // }
}
