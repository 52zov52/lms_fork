<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDocumentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'status',
        'requester_id',
        'academic_year',
        'half_year'
    ];
}
