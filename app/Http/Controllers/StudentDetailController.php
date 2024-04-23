<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class StudentDetailController extends Controller
{
    public function index(): View
    {
        return view('student-primary-details', [
            //
        ]);
    }    
}
