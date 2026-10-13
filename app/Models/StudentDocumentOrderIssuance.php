<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocumentOrderIssuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_document_order_request_id',
        'document_number',
        'last_name',
        'first_name',
        'middle_name',
        'last_name_genitive',
        'first_name_genitive',
        'middle_name_genitive',
        'last_name_dative',
        'first_name_dative',
        'middle_name_dative',
        'birthday',
        'sex',
        'course',
        'level_education_name',
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
        'confirmiter_id',
        'signer_position',
        'signer_fio',
        'characteristic_type',
        'original_characteristic_type',
        'had_academic_leave',
        'leave_orders',
        'is_expelled',
        'show_leave_orders',
        'confirmed_at',
    ];

    protected $casts = [
        'is_budget'          => 'boolean',
        'had_academic_leave' => 'boolean',
        'is_expelled'        => 'boolean',
        'show_leave_orders'  => 'boolean',
        'confirmed_at'       => 'datetime',
    ];
}