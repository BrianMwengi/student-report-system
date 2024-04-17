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

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(ClassForm::class, 'form', 'id');
    }
}

