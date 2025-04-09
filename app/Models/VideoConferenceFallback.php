<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoConferenceFallback extends Model
{
    use HasFactory;

    protected $fillable = [
        'room',
        'link',
        'number_students',
        'total_number_students',
        'schedule_id',
        'date_lesson',
        'time_start_lesson',
        'time_end_lesson',
        'teacher_id',
        'student_id',
        'is_activated',
    ];
}
