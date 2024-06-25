<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\Student;
use App\Models\ClassForm;
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
}
