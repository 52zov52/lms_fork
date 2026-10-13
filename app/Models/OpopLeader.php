<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OpopLeader extends Model
{
    protected $table = 'opop_leaders';

    protected $fillable = ['fio', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /**
     * «Артамонова Анна Геннадьевна» -> «Артамонова А.Г.»
     */
    public function getShortFioAttribute(): string
    {
        $parts = preg_split('/\s+/u', trim((string) $this->fio));

        $lastName = $parts[0] ?? '';

        $initials = '';
        if (!empty($parts[1])) {
            $initials .= mb_strtoupper(mb_substr($parts[1], 0, 1)) . '.';
        }
        if (!empty($parts[2])) {
            $initials .= mb_strtoupper(mb_substr($parts[2], 0, 1)) . '.';
        }

        return trim($lastName . ' ' . $initials);
    }
}