<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTaskList extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fiallable = [
        'task_list_name',
        'color',
        'project_board_id'
    ];
}
