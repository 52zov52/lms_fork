<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSptConsentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'academic_year',
        'half_year',
        'consent_status',
        'signature'
    ];
}
