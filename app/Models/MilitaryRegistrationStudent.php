<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MilitaryRegistrationStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'reserve_category', 
        'military_rank',
        'military_occupation_profile',
        'full_military_specialty_code', 
        'fitness_category',
        'military_commissariat_name',
        'military_registration_type_general',
        'military_registration_type_special',
        'military_document',
        'document_series',
        'document_number',
        'issued_by',
        'issue_date',
        'user_id',
        'status',
    ];
}
