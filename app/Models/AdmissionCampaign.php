<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'application_period_start_date',
        'is_active',
    ];
}
