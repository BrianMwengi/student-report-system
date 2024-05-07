<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\View\View;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('students', [
            //
        ]);
    }

    public function edit($id): View
    {
        $student = Student::find($id);
        return view('livewire.students.edit', ['student' => $student]);
    }
}
