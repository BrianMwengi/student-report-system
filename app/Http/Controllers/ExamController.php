<?php
namespace App\Http\Controllers;

use App\Models\Exam;
use Illuminate\View\View;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(): View
    {
        return view('exams', [
            //
        ]);
    }
}

