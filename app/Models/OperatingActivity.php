<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatingActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'activity_direction',
        'description',
        'status',
        'user_id',
        'week_start_date',
        'week_end_date'
    ];
}
