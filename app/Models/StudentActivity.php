<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'responsibilities',
        'clubs',
        'sports',
        'house_comment',
        'teacher_comment',
        'principal_comment',
    ];

    // This function returns the student that a student activity belongs to.
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
