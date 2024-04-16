<?php

namespace App\Models;

use App\Models\ClassForm;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'class_id',
    ];

    public function classForm()
    {
        return $this->belongsTo(ClassForm::class, 'class_id');
    }

}
