<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupStudent extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'group_student';

    protected $fillable = [
        'group_id',
        'user_id'
    ];
}
