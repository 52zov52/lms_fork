<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectBoard extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'board_name',
        'user_id'
    ];
}
