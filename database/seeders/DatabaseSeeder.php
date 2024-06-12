<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ClassForm;
use Illuminate\Database\Seeder;
use Database\Seeders\ExamSeeder;
use Database\Seeders\StudentsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Creates 4 class records (one for each form)
        ClassForm::factory(4)->create();

        // Creates 8 stream records (2 streams for each class)
        Stream::factory(8)->create();

        // Creates 8 subject records
        Subject::factory(8)->create();

        // Call the ExamSeeder & StudentsTableSeeder
        $this->call([
            StudentsTableSeeder::class,
            ExamSeeder::class,
        ]);
    }
}


