<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index($view = 'list', $id = null): View
    {
        // If an ID is provided, show the edit view
        if ($view === 'edit' && $id) {
            return view('students', ['view' => 'edit', 'id' => $id]);
        }

        // If view is 'create', show the create view
        if ($view === 'create') {
            return view('students', ['view' => 'create']);
        }

        // Otherwise, show the list view
        return view('students', ['view' => 'list']);
    }
}
