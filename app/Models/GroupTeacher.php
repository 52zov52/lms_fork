<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupTeacher extends Model
{
    use HasFactory;

    protected $table = 'group_teacher';

    protected $fillable = [
        'group_id',
        'teacher_id',
        'is_active',
        'academic_year'
    ];
}
