<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    
    public function definition()
    {
        return [
            // Randomly select a student ID from the Student model
            'student_id' => Student::all()->random()->id,
            
            // Randomly select a subject ID from the Subject model
            'subject_id' => Subject::all()->random()->id,
            
            // Generate a random number between 0 and 30 for exam1
            'exam1' => $this->faker->numberBetween($min = 0, $max = 30),
            
            // Generate a random number between 0 and 30 for exam2
            'exam2' => $this->faker->numberBetween($min = 0, $max = 30),
            
            // Generate a random number between 0 and 70 for exam3
            'exam3' => $this->faker->numberBetween($min = 0, $max = 70),
            
            // Generate a random name for the teacher
            'teacher' => $this->faker->name,
        ];
    }
}
