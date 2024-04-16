<?php

namespace App\Http\Controllers;

use App\Models\ClassForm;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class StreamController extends Controller
{
    public function index(): View
    {
        return view('streams', [
            $classes = ClassForm::all(),
            ['classes' => $classes],
        ]);
    }
}
