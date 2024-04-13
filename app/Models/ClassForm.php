<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassForm extends Model
{
    use HasFactory;

    protected $table = 'class_forms';
    
    protected $fillable = [
        'name',
        'form',
    ];
}
