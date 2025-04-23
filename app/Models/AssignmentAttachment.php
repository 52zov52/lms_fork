<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_assignment_id',
        'assignment_comment_id',
        'path_to_file',
        'original_filename',
    ];
}
