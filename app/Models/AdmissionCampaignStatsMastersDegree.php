<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionCampaignStatsMastersDegree extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_campaign_direction_id',
        'admission_campaign_id',
        'applications_count',
        'contracts_count',
        'paid_contracts_count'
    ];
}
