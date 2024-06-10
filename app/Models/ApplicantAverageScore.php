<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantAverageScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'phone',
        'email',
        'user_id',
        'threes_count',
        'fours_count',
        'fives_count',
        'average_score',
        'completed_classes_count',
        'admission_campaign_id',
        'admission_campaign_direction_id'
    ];
}
