<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkStudyPlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'plan_name',
        'speciality_id',
        'teaching_form_id',
        'level_education_id',
        'branch_id',
        'creation_date',
        'approval_date',
        'study_plan_id'
    ];
}
