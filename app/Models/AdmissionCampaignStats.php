<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionCampaignStats extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_campaign_direction_id',
        'admission_campaign_id',
        'applications_certificate_count',
        'applications_diploma_bachelor_count',
        'applications_diploma_college_count',
        'contracts_certificate_count',
        'contracts_diploma_bachelor_count',
        'contracts_diploma_college_count',
        'paid_contracts_count'
    ];
}
