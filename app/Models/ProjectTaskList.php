<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTaskList extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function task() : HasMany
    {
        return $this->hasMany(ProjectTask::class)->orderBy('order');
    }

    protected $fillable = [
        'task_list_name',
        'color',
        'project_board_id'
    ];
}
