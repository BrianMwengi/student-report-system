<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */

 class StudentFactory extends Factory
 {
     protected $model = Student::class;
 
     public function definition()
     {
         return [
             'name' => $this->faker->name,
             'adm_no' => $this->faker->unique()->randomNumber,
             'term' => $this->faker->numberBetween($min = 1, $max = 3), // Assuming term can be 1, 2, or 3
         ];
     }
 
     public function form($form)
     {
         return $this->state(function (array $attributes) use ($form) {
             $stream = Stream::where('name', 'like', "Form {$form}%")->inRandomOrder()->first();
 
             return [
                 'form' => $form,
                 'stream_id' => $stream->id,
             ];
         });
     }
}
