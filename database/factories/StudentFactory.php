<?php

namespace Database\Factories;

use App\Models\Stream;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */

class StudentFactory extends Factory
{
    // Specify the model that this factory is for
    protected $model = Student::class;

    public function definition()
    {
        return [
            // Generate a random name for the student
            'name' => $this->faker->name,
            
            // Generate a unique random admission number for the student
            'adm_no' => $this->faker->unique()->randomNumber,
            
            // Generate a random term number between 1 and 3
            'term' => $this->faker->numberBetween($min = 1, $max = 3), // Assuming term can be 1, 2, or 3
        ];
    }

    public function form($form)
    {
        return $this->state(function (array $attributes) use ($form) {
            // Find a random stream that matches the form number
            $stream = Stream::where('name', 'like', "Form {$form}%")->inRandomOrder()->first();

            return [
                // Set the form number
                'form' => $form,
                
                // Set the stream ID to the ID of the randomly selected stream
                'stream_id' => $stream->id,
            ];
        });
    }
}