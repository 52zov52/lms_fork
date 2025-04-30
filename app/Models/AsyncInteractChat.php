<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsyncInteractChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'half_year',
        'discipline_id',
        'study_thread_normative_id',
        'group_id',
        'user_id',
        'role',
    ];
}
