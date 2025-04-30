<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialPassport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_name',
        'first_name',
        'middle_name',
        'birthday',
        'citizenship',
        'registered_address',
        'housing_conditions',
        'cohabitants',
        'marital_status',
        'spouse_last_name', 
        'spouse_first_name', 
        'spouse_middle_name',
        'spouse_workplace',
        'spouse_phone',
        'family_composition',
        'number_of_children',
        'father_last_name', 
        'father_first_name', 
        'father_middle_name',
        'father_workplace',
        'father_phone',
        'mother_last_name',
        'mother_first_name',
        'mother_middle_name',
        'mother_workplace',
        'mother_phone',
        'father_disability',
        'mother_disability',
        'child_categories',
        'has_pushkin_card',
        'questionnaire_status',
        'confirmiter_id',
        'confirmiter_signature',
        'user_signature'
    ];
}
