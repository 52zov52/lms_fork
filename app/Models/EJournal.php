<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'status',
        'lesson_date',
        'lesson_time',
        'lesson_type',
        'course_teacher_id'
    ];
}
