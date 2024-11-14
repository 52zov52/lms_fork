<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicRetakeSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'retake_date',
        'retake_time',
        'room',
        'academic_year',
        'half_year'
    ];
}
