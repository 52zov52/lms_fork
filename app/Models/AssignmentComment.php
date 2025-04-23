<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_assignment_id',
        'teacher_id',
        'comment',
    ];
}
