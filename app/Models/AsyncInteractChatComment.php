<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsyncInteractChatComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'async_interact_chat_id',
        'user_id',
        'comment',
    ];
}
