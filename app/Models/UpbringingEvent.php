<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpbringingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'name',
        'description',
        'date',
        'academic_year',
        'half_year',
        'user_id',
        'file_path',
        'filename'
    ];
}
