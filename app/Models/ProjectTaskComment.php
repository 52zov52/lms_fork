<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTaskComment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'comment',
        'project_task_id',
        'user_id'
    ];
}
