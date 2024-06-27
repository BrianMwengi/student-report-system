<?php
namespace Database\Factories;

use App\Models\Stream;
use App\Models\ClassForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class StreamFactory extends Factory
{
    protected $model = Stream::class;

    public function definition()
    {
        static $formNumber = 1;
        static $letter = 'A';

        $streamName = "form {$formNumber}{$letter}";

        $letter = $letter === 'A' ? 'B' : 'A';
        if ($letter === 'A') {
            $formNumber++;
        }
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
