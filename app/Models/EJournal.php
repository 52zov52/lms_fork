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
        'date_lesson',
        'time_lesson',
        'course_teacher_id'
    ];
}
