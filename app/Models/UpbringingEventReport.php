<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpbringingEventReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'upbringing_event_id',
        'status',
        'file_path'
    ];
}
