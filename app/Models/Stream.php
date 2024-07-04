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

    /**
     * Get the class form that this stream belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classForm(): BelongsTo
    {
        return $this->belongsTo(ClassForm::class, 'class_id');
    }

}
