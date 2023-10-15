<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentPosition extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'department_position';

    protected $fillable = [
        'department_id',
        'position_id'
    ];
}
