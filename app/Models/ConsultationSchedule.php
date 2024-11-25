<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'discipline_id',
        'consultation_date',
        'consultation_time',
        'room',
        'groups',
        'academic_year',
        'half_year',
        'description',
    ];

    protected $casts = [
        'groups' => 'array',
    ];
}
