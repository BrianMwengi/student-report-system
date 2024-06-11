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
            'student_id' => Student::all()->random()->id,
            'subject_id' => Subject::all()->random()->id,
            'exam1' => $this->faker->numberBetween($min = 0, $max = 30),
            'exam2' => $this->faker->numberBetween($min = 0, $max = 30),
            'exam3' => $this->faker->numberBetween($min = 0, $max = 70),
            'teacher' => $this->faker->name,
        ];
    }
}
