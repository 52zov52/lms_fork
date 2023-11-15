<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DecisionsRectorateAndCouncil extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'position',
        'task',
        'description',
        'due_date',
        'status',
        'file_path',
        'user_id'
    ];
}
