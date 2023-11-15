<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OperatingActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'activity_direction',
        'task',
        'description',
        'status',
        'file_path',
        'user_id'
    ];
}
