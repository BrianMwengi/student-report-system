<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Define the number of students to create in each form
        $studentsPerForm = 50;

        // Loop over each form
        for ($form=1; $form<=4; $form++) {

            // Create students for this form
            for ($i=1; $i<=$studentsPerForm; $i++) {

                // Create a new student with a sequence number
                Student::factory()->form($form)->create([
                    'form_sequence_number' => $i,
                ]);
                                
            }
        }
    }
}

