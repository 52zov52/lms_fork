<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyThreadGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'group_id',
        'student_count',
        'work_study_plan_courses_in_semester_id'
    ];
}
