<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionCampaignStatsPhD extends Model
{
    use HasFactory;

    protected $table = 'admission_campaign_stats_phd';

    protected $fillable = [
        'admission_campaign_direction_id',
        'admission_campaign_id',
        'applications_count',
        'contracts_count',
        'paid_contracts_count'
    ];
}
