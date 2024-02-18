<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'group_id',
        'teacher_id',
        'is_active',
        'academic_year'
    ];
}
