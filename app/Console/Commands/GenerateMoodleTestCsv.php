<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Генерация тестового CSV «выгрузки из Moodle» для вкладки
 * «Управление контингентом → Синхронизация с Moodle».
 *
 * Читает студентов из базы ЛК (те же джойны/фильтры, что getLkStudents())
 * и пишет CSV в формате запроса getMoodleQuery():
 *   st_ticket,last_name,first_name,email,group_name_fixed
 * где колонка last_name = значение Moodle `firstname`,
 * а колонка first_name = значение Moodle `lastname`.
 *
 * Раскладки и расхождения (seed фиксирован опцией --seed):
 *   --match=70     % совпадают (раскладка случайная: перевёрнутая/стандартная)
 *   --no-patronymic=8  % без отчества в Moodle (ожидается тип fio)
 *   --typo=5       % опечатка в фамилии (ожидается тип fio)
 *   --transfer=5   % другая группа (ожидается тип transfer)
 *   --expel=5      % только в Moodle (ожидается тип expel)
 *   остаток        % пропущены в CSV — только в ЛК (ожидается тип enroll)
 *
 * Примеры:
 *   php artisan moodle:test-csv --out=storage/app/moodle_3000.csv
 *   php artisan moodle:test-csv --count=500 --seed=7 --delimiter=";" --out=moodle_500.csv
 */
class GenerateMoodleTestCsv extends Command
{
    protected $signature = 'moodle:test-csv
        {--count=3000 : Сколько студентов ЛК взять (по алфавиту групп/ФИО)}
        {--seed=42 : Seed генератора расхождений (воспроизводимость)}
        {--delimiter=, : Разделитель CSV (, или ;)}
        {--match=70 : Процент совпадающих записей}
        {--no-patronymic=8 : Процент записей без отчества в Moodle}
        {--typo=5 : Процент записей с опечаткой в фамилии}
        {--transfer=5 : Процент записей с другой группой}
        {--expel=5 : Процент записей «только в Moodle»}
        {--out= : Путь к CSV (по умолчанию storage/app/moodle_test.csv)}';

    protected $description = 'Сгенерировать тестовую выгрузку Moodle (CSV) со смешанными раскладками ФИО';

    public function handle(): int
    {
        $count = max(1, (int) $this->option('count'));
        $delimiter = $this->option('delimiter') === ';' ? ';' : ',';
        $out = $this->option('out') ?: storage_path('app/moodle_test.csv');
        mt_srand((int) $this->option('seed'));

        $students = $this->loadLkStudents($count);
        if (empty($students)) {
            $this->error('В базе ЛК нет студентов под фильтры синхронизации. Сначала выполните сиды.');
            return self::FAILURE;
        }

        $weights = [
            'match' => (int) $this->option('match'),
            'no_patronymic' => (int) $this->option('no-patronymic'),
            'typo' => (int) $this->option('typo'),
            'transfer' => (int) $this->option('transfer'),
            'expel' => (int) $this->option('expel'),
        ];

        $handle = @fopen($out, 'wb');
        if ($handle === false) {
            $this->error("Не удалось открыть файл для записи: {$out}");
            return self::FAILURE;
        }

        fputcsv($handle, ['st_ticket', 'last_name', 'first_name', 'email', 'group_name_fixed'], $delimiter);

        $stats = ['match' => 0, 'no_patronymic' => 0, 'typo' => 0, 'transfer' => 0, 'expel' => 0, 'enroll' => 0];
        $groups = array_values(array_unique(array_column($students, 'group')));
        $fakeTicket = 900001;

        foreach ($students as $st) {
            $scenario = $this->pickScenario($weights);

            if ($scenario === 'enroll') {
                // Пропускаем строку: студент только в ЛК.
                $stats['enroll']++;
                continue;
            }

            if ($scenario === 'expel') {
                // Фейковый студент только в Moodle.
                [$last, $first, $middle] = $this->randomFio();
                $fakeLogin = 'st' . ($fakeTicket++);
                $this->writeMoodleRow($handle, $delimiter, $fakeLogin, $last, $first, $middle, $st['group'], $fakeLogin . '@iubip.ru', true);
                $stats['expel']++;
                continue;
            }

            $last = $st['last_name'];
            $first = $st['first_name'];
            $middle = $st['middle_name'];
            $group = $st['group'];

            if ($scenario === 'no_patronymic') {
                $middle = '';
            } elseif ($scenario === 'typo') {
                $last = $this->addTypo($last);
            } elseif ($scenario === 'transfer') {
                $group = $groups[mt_rand(0, count($groups) - 1)];
            }

            // Случайная раскладка Moodle: 60% перевёрнутая, 40% стандартная.
            $reversed = mt_rand(1, 100) <= 60;
            $this->writeMoodleRow($handle, $delimiter, $st['login'], $last, $first, $middle, $group, $st['email'], $reversed);
            $stats[$scenario]++;
        }

        fclose($handle);

        $this->info("CSV записан: {$out} (" . count($students) . ' студентов ЛК).');
        $this->line('Ожидаемые типы изменений при сверке:');
        $this->line("  совпадения (без изменений): {$stats['match']}");
        $this->line("  fio (нет отчества + опечатки): " . ($stats['no_patronymic'] + $stats['typo']));
        $this->line("  transfer: {$stats['transfer']}");
        $this->line("  expel: {$stats['expel']}");
        $this->line("  enroll: {$stats['enroll']}");

        return self::SUCCESS;
    }

    /**
     * Студенты ЛК теми же джойнами/фильтрами, что getLkStudents().
     *
     * @return array<int, array{login: string, last_name: string, first_name: string, middle_name: string, email: string, group: string}>
     */
    private function loadLkStudents(int $count): array
    {
        $rows = DB::select("
            SELECT
                CONCAT('st', sub.latest_ticket) AS st_ticket,
                sub.last_name AS last_name,
                sub.first_name AS first_name,
                sub.middle_name AS middle_name,
                CONCAT(sub.latest_ticket, '_', YEAR(sub.year_admission), '@iubip.ru') AS email,
                CONCAT(
                    REPLACE(
                        REPLACE(REPLACE(sub.group_name, ' ', ''), YEAR(CURDATE()), ''),
                        CONCAT('(', YEAR(CURDATE()), ')'), ''
                    ),
                    ' (', YEAR(CURDATE()), ')'
                ) AS group_name_fixed
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
            ORDER BY
                sub.group_name, sub.last_name, sub.first_name
            LIMIT {$count}
        ");

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'login' => $row->st_ticket,
                'last_name' => $row->last_name,
                'first_name' => $row->first_name,
                'middle_name' => $row->middle_name ?? '',
                'email' => $row->email,
                'group' => $row->group_name_fixed,
            ];
        }

        return $result;
    }

    /**
     * @param  array{match: int, no_patronymic: int, typo: int, transfer: int, expel: int}  $weights
     */
    private function pickScenario(array $weights): string
    {
        $roll = mt_rand(1, 100);
        $acc = 0;
        foreach ($weights as $scenario => $weight) {
            $acc += $weight;
            if ($roll <= $acc) {
                return $scenario;
            }
        }

        return 'enroll';
    }

    /**
     * Одна строка Moodle-CSV.
     * Колонка last_name = Moodle `firstname`, колонка first_name = Moodle `lastname`.
     */
    private function writeMoodleRow(
        $handle,
        string $delimiter,
        string $login,
        string $last,
        string $first,
        string $middle,
        string $group,
        string $email,
        bool $reversed
    ): void {
        $nameWithPatronymic = trim($first . ' ' . $middle);
        if ($reversed) {
            // Moodle firstname = Фамилия, Moodle lastname = «Имя Отчество».
            $colLastName = $last;
            $colFirstName = $nameWithPatronymic;
        } else {
            // Moodle firstname = «Имя Отчество», Moodle lastname = Фамилия.
            $colLastName = $nameWithPatronymic;
            $colFirstName = $last;
        }

        fputcsv($handle, [$login, $colLastName, $colFirstName, $email, $group], $delimiter);
    }

    /**
     * Одна опечатка в фамилии: дублирование случайной буквы.
     */
    private function addTypo(string $last): string
    {
        $len = mb_strlen($last);
        if ($len < 2) {
            return $last . $last;
        }
        $pos = mt_rand(0, $len - 1);
        $char = mb_substr($last, $pos, 1);

        return mb_substr($last, 0, $pos + 1) . $char . mb_substr($last, $pos + 1);
    }

    /**
     * @return array{0: string, 1: string, 2: string}
     */
    private function randomFio(): array
    {
        $lasts = ['Захаров', 'Ершов', 'Григорьев', 'Тихонов', 'Лазарев', 'Матвеева', 'Родионова'];
        $pairs = [
            ['Олег', 'Олегович'], ['Роман', 'Романович'], ['Тимур', 'Тимурович'],
            ['Вера', 'Валерьевна'], ['Надежда', 'Павловна'], ['Любовь', 'Фёдоровна'],
        ];
        $pair = $pairs[mt_rand(0, count($pairs) - 1)];

        return [$lasts[mt_rand(0, count($lasts) - 1)], $pair[0], $pair[1]];
    }
}
