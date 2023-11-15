<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatingActivityTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function taskList() : BelongsTo
    {
        return $this->belongsTo(OperatingActivityTaskList::class);
    }

    protected $fillable = [
        'task_name',
        'due_date',
        'description',
        'status',
        'order',
        'file_path',
        'operating_activity_task_list_id',
        'user_id'
    ];
}
