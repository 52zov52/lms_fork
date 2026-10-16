<?php

namespace App\Http\Controllers\Report\Concerns;

use App\Models\User;
use Illuminate\Support\Carbon;

trait MakesFileNames
{
    /**
     * Собирает безопасное имя .docx из частей.
     * Пример: $this->fileName('Справка', $fio, now(), 'docx')
     *         => "Справка - Иванов И.И. - 14.10.2026.docx"
     */
    protected function fileName(string ...$parts): string
    {
        $parts = array_filter(array_map(function ($p) {
            if ($p instanceof Carbon || $p instanceof \DateTimeInterface) {
                return Carbon::parse($p)->format('d.m.Y');
            }
            return $this->sanitizeFileName((string)$p);
        }, $parts));

        return implode(' - ', $parts) . '.docx';
    }

    /** ФИО в формате "Иванов И.И." (или "Иванов И. И." — на ваш вкус) */
    protected function shortFio($user): string
    {
        $last   = trim((string)($user->last_name   ?? ''));
        $first  = trim((string)($user->first_name  ?? ''));
        $middle = trim((string)($user->middle_name ?? ''));

        if ($last === '') {
            return 'без ФИО';
        }

        $fio = $last;
        if ($first  !== '') $fio .= ' ' . mb_substr($first,  0, 1) . '.';
        if ($middle !== '') $fio .= mb_substr($middle, 0, 1) . '.';

        return $fio;
    }

    protected function sanitizeFileName(string $name): string
    {
        $name = preg_replace('/[\\\\\/:*?"<>|\r\n\t]+/u', ' ', $name);
        $name = preg_replace('/\s+/u', ' ', $name);
        return trim($name, " .\t");
    }
}