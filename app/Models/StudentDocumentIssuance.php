<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocumentIssuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_document_request_id',
        'document_number',
        'last_name',
        'first_name',
        'middle_name',
        'last_name_dative',
        'first_name_dative',
        'middle_name_dative',
        'sex',
        'course',
        'teaching_form_name',
        'speciality_name',
        'specialty_code',
        'orders',
        'training_period',
        'check_in_date',
        'check_out_date',
        'check_in_duration',
        'employment_place',
        'is_budget',
        'confirmiter_id'
    ];
}
