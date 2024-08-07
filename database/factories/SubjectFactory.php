<?php

namespace Database\Factories;

use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */

 class SubjectFactory extends Factory
 {
     /**
      * The name of the factory's corresponding model.
      *
      * @var string
      */
     protected $model = Subject::class;
 
     /**
      * Define the model's default state.
      *
      * @return array
      */

    public function definition()
    {
        // Static variable to keep track of the order
        static $order = 0;
    
        // List of subjects
        $subjects = [
            'English', 'Kiswahili', 'Mathematics', 'Biology', 'Physics',
            'Chemistry', 'History', 'Geography', 'CRE', 'Agriculture',
            'Computer Studies', 'Business Studies'
        ];
    
        return [
            // Assign a subject name from the list, cycling through the list using modulo
            'name' => $subjects[$order++ % count($subjects)],
        ];
    }
}