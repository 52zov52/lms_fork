<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRespons extends Model
{
    use HasFactory;

    protected $fillable = [
        'study_year',
        'half_year',
        'discipline_id',
        'study_thread_normative_id',
        'group_id',
    ];
}
