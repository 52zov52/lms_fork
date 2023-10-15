<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentOrderRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'document_type',
        'document_number',
        'status',
        'requester_id',
        'issuer_id',
        'request_date',
        'issue_date'
    ];
}
