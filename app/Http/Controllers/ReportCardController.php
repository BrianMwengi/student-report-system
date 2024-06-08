<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class ReportCardController extends Controller
{
    /**
     * Display the report card view for a specific student.
     *
     * @param int $studentId The ID of the student.
     * @return \Illuminate\View\View The view for the report card.
     */
    public function index(int $studentId): View
    {
        // Pass the selected student ID to the view.
        return view('reports', ['selectedStudentId' => $studentId]);
    }
}
