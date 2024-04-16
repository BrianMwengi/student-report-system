<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassFormController;

Route::view('/', 'welcome');

Route::get('classforms', [ClassFormController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('classforms'); 

Route::get('subjects', [SubjectController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('subjects'); 

Route::get('streams', [StreamController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('streams'); 

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
