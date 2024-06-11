<?php

namespace Database\Factories;

use App\Models\Stream;
use App\Models\ClassForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stream>
 */
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

        // Assuming the ClassModel instances have been created before the Stream instances
        $class_id = ClassForm::where('name', 'Form ' . $formNumber)->first()->id;

        return [
            'name' => $streamName,
            'class_id' => $class_id,
        ];
    }
}
