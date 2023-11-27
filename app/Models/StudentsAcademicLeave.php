<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentsAcademicLeave extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'group_id',
        'academic_leave_start_date',
        'academic_leave_end_date',
        'description',
        'is_academic_leave'
    ];
}
