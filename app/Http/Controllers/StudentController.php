<?php

namespace App\Http\Controllers;

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