<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
