<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperatingActivityFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'operating_activity_id',
        'file_path',
        'filename'
    ];
}
