<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserData extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable = [
        'last_name',
        'first_name',
        'middle_name',
        'birthday',
        'birthplace',
        'sex',
        'document_id',
        'passport_series',
        'passport_number',
        'passport_issue_date',
        'passport_issuing_authority',
        'division_code',
        'snils',
        'inn',
        'family_status_id',
        'is_residence',
        'citizenship',
        'country',
        'last_name_genitive',
        'first_name_genitive',
        'middle_name_genitive',
        'last_name_dative',
        'first_name_dative',
        'middle_name_dative',
        'branch_id',
        'user_id'
    ];
}
