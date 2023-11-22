<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentStatusType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'status_type_name'
    ];
}
