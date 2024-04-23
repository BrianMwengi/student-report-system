<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'exam1',
        'exam2',
        'exam3',
        'average',
        'grade',
        'points',
        'position',
        'remarks',
        'teacher',
    ];

    // This function returns the student that an exam belongs to.
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
    
   // This function returns the subject that an exam belongs to.
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
