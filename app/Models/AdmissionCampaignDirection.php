<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionCampaignDirection extends Model
{
    use HasFactory;

    protected $fillable = [
        'speciality_code',
        'speciality_name',
        'speciality_profile',
        'level_education_id',
        'teaching_form_id',
        'funding_type',
        'admission_campaign_id',
        'is_active'
    ];
}
