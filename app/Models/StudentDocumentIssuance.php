<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocumentIssuance extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'status',
        'requester_id',
        'academic_year'
    ];
}
