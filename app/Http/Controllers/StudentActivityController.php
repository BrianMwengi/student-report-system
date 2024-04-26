<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentActivityController extends Controller
{
    public function index(): View
    {
        return view('studentactivities', [
            //
        ]);
    }  
}
