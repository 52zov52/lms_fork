<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DecisionsRectorateAndCouncil extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'decisions_rectorate_and_council';

    protected $fillable = [
        'position',
        'task',
        'description',
        'due_date',
        'status',
        'category',
        'file_path',
        'user_id'
    ];
}
