<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'order_user';

    protected $fillable = [
        'order_id',
        'user_id',
        'notes'
    ];
}
