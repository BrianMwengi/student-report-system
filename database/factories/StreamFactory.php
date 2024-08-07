<?php
namespace Database\Factories;

use App\Models\Stream;
use App\Models\ClassForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class StreamFactory extends Factory
{
    // Specify the model that this factory is for
    protected $model = Stream::class;

    public function definition()
    {
        // Static variable to keep track of the form number
        static $formNumber = 1;
        
        // Static variable to keep track of the letter
        static $letter = 'A';

        // Create the stream name using the form number and letter
        $streamName = "form {$formNumber}{$letter}";

        // Toggle the letter between 'A' and 'B'
        $letter = $letter === 'A' ? 'B' : 'A';
        
        // If the letter is reset to 'A', increment the form number
        if ($letter === 'A') {
            $formNumber++;
        }
        
        // If the form number exceeds 4, reset it to 1
        if ($formNumber > 4) {
            $formNumber = 1;
        }

        // Ensure a ClassForm instance exists or create one
        $classForm = ClassForm::firstOrCreate(['name' => 'Form ' . $formNumber], ['name' => 'Form ' . $formNumber]);

        return [
            'name' => $streamName,
            'class_id' => $classForm->id,
        ];
    }
}
