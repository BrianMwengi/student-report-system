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
         static $order = 0;
 
         $subjects = [
             'English', 'Kiswahili', 'Mathematics', 'Biology', 'Physics',
             'Chemistry', 'History', 'Geography', 'CRE', 'Agriculture',
             'Computer Studies', 'Business Studies'
         ];
 
         return [
             'name' => $subjects[$order++ % count($subjects)],
         ];
     }
 }
 