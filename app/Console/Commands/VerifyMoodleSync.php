<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Проверка сверки ЛК ↔ Moodle без браузера.
 *
 * Повторяет логику MoodleContingentSync::compare() один в один:
 * матчинг строго по логину, ФИО — неупорядоченным набором слов,
 * группы — регистронезависимо. Печатает счётчики совпадений и
 * изменений по типам (fio/transfer/expel/enroll).
 *
 * Пример:
 *   php artisan moodle:verify-sync --csv=storage/app/moodle_3000.csv
 */
class VerifyMoodleSync extends Command
{
    protected $signature = 'moodle:verify-sync
        {--csv= : Путь к CSV Moodle (по умолчанию storage/app/moodle_3000.csv)}';

    protected $description = 'Сверить БД ЛК с CSV Moodle и показать счётчики синхронизации';

    public function handle(): int
    {
        $csv = $this->option('csv') ?: storage_path('app/moodle_3000.csv');
        if (! is_file($csv)) {
            $csv = base_path('moodle_3000.csv');
        }
        if (! is_file($csv)) {
            $this->error('CSV не найден.');
            return self::FAILURE;
        }

        $moodle = $this->parseCsv($csv);
        $this->line('Строк Moodle CSV: ' . count($moodle));

        try {
            $lk = $this->loadLkStudents();
        } catch (\Throwable $e) {
            $this->error('Запрос ЛК упал: ' . $e->getMessage());
            return self::FAILURE;
        }
        $this->line('Студентов ЛК (после фильтров): ' . count($lk));

        $match = $fio = $transfer = $expel = $enroll = 0;
        $samples = ['fio' => [], 'transfer' => [], 'expel' => [], 'enroll' => []];

        foreach ($lk as $login => $lkRow) {
            if (! isset($moodle[$login])) {
                $enroll++;
                if (count($samples['enroll']) < 5) {
                    $samples['enroll'][] = $login;
                }
                continue;
            }
            $fioDiff = $this->fioWords($lkRow['fio']) !== $this->fioWords($moodle[$login]['fio']);
            $groupDiff = $this->norm($lkRow['group']) !== $this->norm($moodle[$login]['group']);
            if (! $fioDiff && ! $groupDiff) {
                $match++;
            }
            if ($fioDiff) {
                $fio++;
                if (count($samples['fio']) < 5) {
                    $samples['fio'][] = $login;
                }
            }
            if ($groupDiff) {
                $transfer++;
                if (count($samples['transfer']) < 5) {
                    $samples['transfer'][] = $login;
                }
            }
        }

        foreach ($moodle as $login => $row) {
            if (! isset($lk[$login])) {
                $expel++;
                if (count($samples['expel']) < 5) {
                    $samples['expel'][] = $login;
                }
            }
        }

        $this->info('Совпадения (без изменений): ' . $match);
        $this->info('ФИО: ' . $fio . '  [' . implode(', ', $samples['fio']) . ']');
        $this->info('Переводы: ' . $transfer . '  [' . implode(', ', $samples['transfer']) . ']');
        $this->info('Отчисления: ' . $expel . '  [' . implode(', ', $samples['expel']) . ']');
        $this->info('Зачисления: ' . $enroll . '  [' . implode(', ', $samples['enroll']) . ']');

        return self::SUCCESS;
    }

    /** @return array<string, array{fio: string, group: string}> */
    private function parseCsv(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            return [];
        }
        if (! mb_check_encoding($raw, 'UTF-8')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'windows-1251');
        }
        $lines = preg_split("/\r\n|\n|\r/", $raw);
        $lines = array_values(array_filter($lines, fn ($l) => trim($l) !== ''));
        if (empty($lines)) {
            return [];
        }
        $delimiter = str_contains($lines[0], ';') ? ';' : ',';
        $header = array_map(fn ($h) => mb_strtolower(trim($h)), str_getcsv($lines[0], $delimiter, '"', '"'));
        $map = [];
        foreach ($header as $i => $h) {
            if (in_array($h, ['st_ticket', 'username', 'логин', 'login'])) {
                $map['login'] = $i;
            } elseif (in_array($h, ['last_name', 'фамилия', 'surname'])) {
                $map['last_name'] = $i;
            } elseif (in_array($h, ['first_name', 'имя', 'name'])) {
                $map['first_name'] = $i;
            } elseif (in_array($h, ['group_name_fixed', 'group', 'группа', 'реальный_группа', 'real_group'])) {
                $map['group'] = $i;
            }
        }

        $result = [];
        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i], $delimiter, '"', '"');
            if (count($row) <= max($map['login'], $map['last_name'], $map['first_name'], $map['group'])) {
                continue;
            }
            $login = trim($row[$map['login']]);
            if ($login === '') {
                continue;
            }
            $result[$login] = [
                'fio' => trim($row[$map['last_name']]) . ' ' . trim($row[$map['first_name']]),
                'group' => trim($row[$map['group']]),
            ];
        }

        return $result;
    }

    /** @return array<string, array{fio: string, group: string}> */
    private function loadLkStudents(): array
    {
        // Тот же запрос, что MoodleContingentSync::getLkStudents().
        $rows = DB::select("
            SELECT
                CONCAT('st', sub.latest_ticket) AS st_ticket,
                CONCAT_WS(' ', sub.last_name, sub.first_name, sub.middle_name) AS fio,
                sub.group_name AS group_name_fixed
            FROM (
                SELECT
                    u.id AS user_id,
                    ud.last_name,
                    ud.first_name,
                    ud.middle_name,
                    g.group_name,
                    g.year_admission,
                    MAX(st.ticket_number) AS latest_ticket
                FROM
                    group_student gs
                    INNER JOIN `groups` g ON gs.group_id = g.id AND g.deleted_at IS NULL
                    INNER JOIN users u ON gs.user_id = u.id AND u.deleted_at IS NULL
                    INNER JOIN users_data ud ON u.id = ud.user_id AND ud.deleted_at IS NULL
                    INNER JOIN student_ticket_numbers st ON u.id = st.user_id AND st.deleted_at IS NULL
                WHERE
                    g.deleted_at IS NULL
                    AND g.year_completion IS NOT NULL
                    AND g.year_completion >= CURDATE()
                    AND g.year_admission >= CURDATE() - INTERVAL 6 YEAR
                    AND gs.deleted_at IS NULL
                    AND g.group_name NOT REGEXP '^[0-9]'
                    AND EXISTS (
                        SELECT 1
                        FROM group_student gs2
                        WHERE gs2.group_id = g.id
                        AND gs2.deleted_at IS NULL
                    )
                GROUP BY
                    u.id, ud.last_name, ud.first_name, ud.middle_name, g.group_name, g.year_admission
            ) sub
        ");

        $result = [];
        foreach ($rows as $row) {
            $result[$row->st_ticket] = ['fio' => $row->fio, 'group' => $row->group_name_fixed];
        }

        return $result;
    }

    private function fioWords(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = str_replace('ё', 'е', $value);
        $value = str_replace(['-', '‐', '‑', '‒', '–', '—', '―'], ' ', $value);
        $words = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        if ($words === false) {
            return '';
        }
        sort($words);

        return implode(' ', $words);
    }

    private function norm(string $value): string
    {
        return mb_strtolower(trim($value));
    }
}
