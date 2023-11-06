<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfirmedDocumentRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'sex',
        'course',
        'teaching_form_name',
        'speciality_name',
        'specialty_code',
        'orders',
        'training_period',
        'is_budget',
        'confirmiter_id',
        'document_order_request_id'
    ];
}
