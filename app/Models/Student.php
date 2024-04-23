<?php

namespace App\Models;

use App\Models\Stream;
use App\Models\ClassForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'adm_no',
        'term',
        'form',
        'stream_id',
        'form_sequence_number',    
    ];

   // This function returns the stream that a student belongs to.
    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    // This function returns the form that a student belongs to.
    public function form(): BelongsTo
    {
        return $this->belongsTo(ClassForm::class, 'form', 'id');
    }

    // This function returns all the exams that belong to a student.
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}

