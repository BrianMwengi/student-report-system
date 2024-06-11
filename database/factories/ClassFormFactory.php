<?php

namespace Database\Factories;

use App\Models\ClassForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassForm>
 */
class ClassModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ClassForm::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $formNumber = 1;

        // Assuming 'name' field is your form number
        $formName = 'Form ' . $formNumber;

        // Increment formNumber for next creation
        $formNumber++;

        // Reset after form 4
        if ($formNumber > 4) {
            $formNumber = 1;
        }

        return [
            // Assuming there is a 'name' field in your 'classes' table
            'name' => $formName,
            // add other fields here...
        ];
    }
}
