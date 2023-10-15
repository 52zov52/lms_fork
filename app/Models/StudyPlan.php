<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudyPlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'plan_name',
        'speciality_id',
        'teaching_form_id',
        'level_education_id',
        'qualification_id',
        'number_semesters',
        'number_years',
        'number_months',
        'start_date',
        'end_date',
        'creation_date',
        'approval_date'
    ];
}
