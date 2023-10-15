<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Speciality extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'speciality_name',
        'specialty_code',
        'level_education',
        'qualification_id',
        'department_position_id'
    ];
}
