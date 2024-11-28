<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRetakeBulletin extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_grades_id',
        'retake_bulletin_number',
        'type_bulletin_id',
        'opening_date',
        'closing_date',
        'pay_number',
        'pay_date',
        'grade',
        'teacher_id',
        'description',
        'external_id'
    ];
}
