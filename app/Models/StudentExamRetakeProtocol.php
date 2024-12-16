<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExamRetakeProtocol extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_study_plan_course_control_types_in_semesters_id',
        'grade',
        'type_protocol',
        'external_id'
    ];
}
