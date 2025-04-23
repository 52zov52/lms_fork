<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResponseComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_attachment_id',
        'teacher_id',
        'comment',
    ];
}
