<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_respons_id',
        'student_id',
        'path_to_file',
        'original_filename',
        'comment',
    ];
}
