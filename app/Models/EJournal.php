<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EJournal extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'status',
        'lesson_date',
        'lesson_time',
        'lesson_type',
        'lesson_theme',
        'course_teacher_id',
        'who_checked',
        'checked_at',
        'is_signature',
        'who_signed',
        'signatured_at'
    ];
}
