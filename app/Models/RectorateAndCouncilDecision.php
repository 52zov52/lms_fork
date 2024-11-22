<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RectorateAndCouncilDecision extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_decision',
        'number_decision',
        'position',
        'task',
        'due_date',
        'status',
        'category',
        'operating_activity_id',
        'executor_id',
        'user_id'
    ];
}
