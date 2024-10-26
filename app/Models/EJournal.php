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
        'teacher_id',
        'work_study_plan_courses_in_semester_id',
        'group_id',
        'student_id',
        'status',
        'lesson_date',
        'lesson_time',
        'lesson_type',
        'lesson_theme',
        'who_checked',
        'checked_at',
        'is_signature',
        'who_signed',
        'signatured_at',
        'academic_year',
        'half_year'
    ];
}
