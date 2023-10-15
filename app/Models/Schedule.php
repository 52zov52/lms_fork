<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'group',
        'course',
        'lesson_number',
        'day',
        'week',
        'date',
        'time',
        'lesson_place',
        'discipline',
        'lesson_type',
        'teacher',
        'chair'
    ];
}
