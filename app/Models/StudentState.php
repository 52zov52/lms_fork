<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentState extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'student_state';

    protected $fillable = [
        'user_id',
        'state_id',
        'group_id'
    ];
}
