<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsyncInteractChatFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'async_interact_chat_comment_id',
        'path_to_file',
        'original_filename',
    ];
}
