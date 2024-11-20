<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFamilyStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_married',
        'marriage_date',
        'is_divorced',
        'divorce_date',
        'is_widowed',
        'previous_marriage_1_start',
        'previous_marriage_1_end',
        'previous_marriage_2_start',
        'previous_marriage_2_end',
        'previous_marriage_3_start',
        'previous_marriage_3_end',
        'previous_marriage_4_start',
        'previous_marriage_4_end',
        'previous_marriage_5_start',
        'previous_marriage_5_end',
        'is_spouse_studying',
        'is_spouse_in_iubip',
        'has_children',
        'child_1_birth_date',
        'child_2_birth_date',
        'child_3_birth_date',
        'child_4_birth_date',
        'child_5_birth_date',
        'is_single_parent',
        'confirmiter_id',
        'confirmiter_signature',
        'user_signature'
    ];
}
