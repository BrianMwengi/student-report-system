<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    public function index($studentId): View
    {
        return view('reports', ['selectedStudentId' => $studentId]);
    }
}
