<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyThreadSubGroupUser extends Model
{
    use HasFactory;

    protected $table = 'study_thread_subgroup_user';

    protected $fillable = [
        'study_thread_subgroup_id',
        'user_id'
    ];
}
