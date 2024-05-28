<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionCampaignStatsCollege extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_campaign_direction_id',
        'admission_campaign_id',
        'applications_after_nine_grades_count',
        'applications_after_eleventh_grades_count',
        'contracts_after_nine_grades_count',
        'contracts_after_eleventh_grades_count',
        'paid_contracts_count'
    ];
}
