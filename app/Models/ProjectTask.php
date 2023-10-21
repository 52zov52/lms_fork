<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function taskList() : BelongsTo
    {
        return $this->belongsTo(ProjectTaskList::class);
    }

    protected $fillable = [
        'task_name',
        'due_date',
        'project_task_list_id',
        'order'
    ];
}
