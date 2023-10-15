<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStatus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'user_status';

    protected $fillable = [
        'user_id',
        'status_id',
        'speciality_id',
        'department_position_id'
    ];
}
