<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QRCodeDocumentVerification extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'qr_code_document_verifications';

    protected $fillable = [
        'user_id',
        'document_number',
        'document_date',
        'verification_code',
        'description'
    ];
}
