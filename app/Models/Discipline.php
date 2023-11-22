<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discipline extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'discipline_name',
        'discipline_short_name'
    ];
}
