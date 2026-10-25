<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Тестовая база ЛК по реальной выгрузке Moodle (mdl_user (15).csv).
 *
 * База строится «как в файле», после чего к ней применяются контролируемые
 * изменения — ровно те, что проверяет страница
 * «Управление контингентом → Синхронизация с Moodle»:
 *
 *   ┌────────────────────┬──────────────────────────────────────────────┐
 *   │ Сценарий           │ Как выглядит в ЛК относительно CSV Moodle     │
 *   ├────────────────────┼──────────────────────────────────────────────┤
 *   │ Зачисления         │ студент есть в ЛК, но его нет в Moodle         │
 *   │ Отчисления         │ студента нет в ЛК (есть только в Moodle)       │
 *   │ Смена ФИО          │ в ЛК полное ФИО с отчеством, в Moodle — без    │
 *   │ Перевод            │ в ЛК другая группа                             │
 *   │ Доп. набор (relink)│ в Moodle временный vr-логин, в ЛК — st-логин   │
 *   └────────────────────┴──────────────────────────────────────────────┘
 *
 * Категории Moodle:
 *   1 «Стандарт»   — студенты в актуальной группе  → основа ЛК;
 *   2 «Свободные»  — без группы в Moodle. Это бывшие студенты/академ.
 *                    отпуска, в активный контингент ЛК не входят, поэтому
 *                    в ЛК не создаются (сверка их корректно игнорирует и
 *                    не пытается отчислить);
 *   3 «Доп. набор» — временный логин vr*. В ЛК создаются с настоящим
 *                    st-билетом → сверка выдаёт операцию relink.
 *
 * Данные студента (ФИО, билет, группа) детерминированы (SEED), поэтому
 * повторный запуск даёт тот же результат.
 *
 * Запуск:
 *   php artisan db:seed --class=MoodleRealCsvFixtureSeeder
 *
 * Путь к CSV: MOODLE_REAL_CSV, иначе storage/app/mdl_user (15).csv,
 * иначе ./mdl_user (15).csv. Реальную выгрузку с ПДн НЕ коммитить.
 */
class MoodleRealCsvFixtureSeeder extends Seeder
{
    private const CHUNK = 500;

    private const SEED = 2026;

    /** Сколько строк CSV показать в каждом сценарии. */
    private const N_FIO = 15;

    private const N_TRANSFER = 15;

    private const N_ENROLL = 8;

    private const N_EXPEL = 8;

    /** Билеты для «Доп. набора»: vr-логин → настоящий st-билет. */
    private const EXTRA_TICKET_START = 960001;

    /** Билеты для cat-1 строк с нестандартным логином (school105, abiturbak…). */
    private const PLAIN_TICKET_START = 970001;

    /** Билеты для студентов, которые есть только в ЛК (зачисления). */
    private const ENROLL_TICKET_START = 950001;

    private const EMAIL_DOMAIN = 'fixture.test';

    /** Редкие фамилии для «зачислений» — их заведомо нет в выгрузке Moodle. */
    private const ENROLL_NAMES = [
        ['Новозачисленко', 'Иван', 'Петрович'],
        ['Первокурсникова', 'Анна', 'Сергеевна'],
        ['Зачисленков', 'Максим', 'Олегович'],
        ['Новоприбылова', 'Мария', 'Андреевна'],
        ['Свежекурсов', 'Артём', 'Дмитриевич'],
        ['Новобранченко', 'Елена', 'Викторовна'],
        ['Приёмников', 'Даниил', 'Игоревич'],
        ['Поступиленко', 'Полина', 'Максимовна'],
        ['Новокурсников', 'Сергей', 'Валерьевич'],
        ['Зачисляева', 'Ольга', 'Николаевна'],
    ];

    public function run(): void
    {
        // Справочники, роли и демо-аккаунты (branch/document/family status/планы).
        $this->call(ContingentSeeder::class);

        $csvPath = $this->csvPath();
        if (! is_file($csvPath)) {
            $this->command->error("CSV не найден: {$csvPath}");
            $this->command->line('Укажите путь через MOODLE_REAL_CSV=/path/to/mdl_user.csv');

            return;
        }

        $rows = $this->readCsv($csvPath);
        if ($rows === []) {
            $this->command->error('CSV пуст или не распознан.');

            return;
        }

        $this->command->info('Строк CSV (уникальных логинов): '.count($rows));

        // ── Раскладка по категориям ──
        $cat1 = [];
        $cat3 = [];
        $csvTickets = [];
        foreach ($rows as $login => $r) {
            if ($r['category'] === 1) {
                $cat1[] = $login;
            } elseif ($r['category'] === 3) {
                $cat3[] = $login;
            }
            if ($r['ticket'] !== null) {
                $csvTickets[$r['ticket']] = true;
            }
        }
        sort($cat1);
        sort($cat3);
        $this->command->line(sprintf(
            '  категории: 1=%d, 2=%d, 3=%d',
            count($cat1),
            count(array_filter($rows, fn ($r) => $r['category'] === 2)),
            count($cat3)
        ));

        // ── Детерминированный выбор сценариев ──
        mt_srand(self::SEED);

        $expelPool = array_values(array_filter($cat1, fn ($l) => $this->isStLogin($l)));
        shuffle($expelPool);
        $expel = array_slice($expelPool, 0, self::N_EXPEL);
        $expelSet = array_flip($expel);

        $rest = array_values(array_filter($cat1, fn ($l) => ! isset($expelSet[$l])));

        // ФИО: только строки без отчества (2 слова), чтобы «в ЛК добавилось отчество».
        $fioPool = array_values(array_filter(
            $rest,
            fn ($l) => $this->isStLogin($l) && $this->fioWordCount($rows[$l]) === 2
        ));
        shuffle($fioPool);
        $fio = array_slice($fioPool, 0, self::N_FIO);
        $fioSet = array_flip($fio);

        $rest2 = array_values(array_filter($rest, fn ($l) => ! isset($fioSet[$l])));
        $transferPool = array_values(array_filter($rest2, fn ($l) => $this->isStLogin($l)));
        shuffle($transferPool);
        $transfer = array_slice($transferPool, 0, self::N_TRANSFER);
        $transferSet = array_flip($transfer);

        $this->command->info(sprintf(
            'План: зачисления=%d, отчисления=%d, ФИО=%d, переводы=%d (+составные группы), доп. набор=%d',
            self::N_ENROLL,
            count($expel),
            count($fio),
            count($transfer),
            count($cat3)
        ));

        // ── Снос предыдущей фикстуры ──
        // Демо-студентов student1..6 (tickets 100001..100006) тоже убираем:
        // их нет в реальной выгрузке, иначе они попали бы в «Зачисления»
        // случайным шумом. Учётные записи admin/employee/teacher/student
        // не трогаем.
        $demoEmails = [
            'student1@i.ru', 'student2@i.ru', 'student3@i.ru',
            'student4@i.ru', 'student5@i.ru', 'student6@i.ru',
        ];
        $wipeIds = DB::table('users')
            ->where('email', 'like', '%@'.self::EMAIL_DOMAIN)
            ->orWhere('email', 'like', '%@load.test')
            ->orWhereIn('email', $demoEmails)
            ->pluck('id')
            ->all();
        if ($wipeIds !== []) {
            foreach (array_chunk($wipeIds, 1000) as $chunkIds) {
                DB::table('group_student')->whereIn('user_id', $chunkIds)->delete();
                DB::table('users_data')->whereIn('user_id', $chunkIds)->delete();
                DB::table('student_ticket_numbers')->whereIn('user_id', $chunkIds)->delete();
                DB::table('users')->whereIn('id', $chunkIds)->delete();
            }
            $this->command->line('Удалено пользователей прошлой фикстуры: '.count($wipeIds));
        }

        // ── Группы из CSV ──
        // В ЛК название группы хранится БЕЗ суффикса « (год)»: SQL-запрос
        // getLkStudents сам добавляет текущий год. Для составных названий
        // («A (2026), B (2026)») берём первую группу — в ЛК студент состоит
        // в одной группе, вторая попадёт в перевод.
        $groupBaseOf = [];
        $bases = [];
        foreach ($rows as $login => $r) {
            $base = $this->groupBase($r['groupRaw']);
            $groupBaseOf[$login] = $base;
            if ($base !== '') {
                $bases[$base] = true;
            }
        }
        $bases = array_keys($bases);
        sort($bases);

        $groupIds = [];
        foreach ($bases as $i => $name) {
            $groupIds[$name] = $this->ensureGroup($name, 930000 + $i);
        }
        $this->command->line('Групп задействовано: '.count($groupIds));

        // ── Сборка студентов ЛК ──
        $now = Carbon::now()->toDateTimeString();
        $passwordHash = Hash::make('password');

        $staged = [];
        $planFio = [];
        $planTransfer = [];
        $planRelink = [];
        $planExpel = [];
        $extraTicket = self::EXTRA_TICKET_START;
        $plainTicket = self::PLAIN_TICKET_START;

        foreach ($rows as $login => $r) {
            // «Свободные»: в активный контингент ЛК не входят.
            if ($r['category'] === 2) {
                continue;
            }

            // Отчисление: строку CSV намеренно не заводим в ЛК.
            if (isset($expelSet[$login])) {
                $planExpel[] = [$login, $this->csvFio($r), $r['groupRaw']];

                continue;
            }

            // Билет: у cat-3 и нестандартных логинов его нет в CSV.
            if ($r['category'] === 3) {
                $ticket = $this->nextFreeTicket($extraTicket, $csvTickets);
            } elseif ($r['ticket'] !== null) {
                $ticket = $r['ticket'];
                $csvTickets[$ticket] = true;
            } else {
                $ticket = $this->nextFreeTicket($plainTicket, $csvTickets);
            }

            // ФИО: для сценария «смена ФИО» в ЛК появляется отчество.
            if (isset($fioSet[$login])) {
                [$last, $first, $middle] = $this->fioVariant($r);
                $planFio[] = [$login, 'st'.$ticket, $this->csvFio($r), trim("{$last} {$first} {$middle}")];
            } else {
                [$last, $first, $middle] = $this->splitFio($r);
            }

            // Группа: для сценария «перевод» берём следующую по кругу.
            $base = $groupBaseOf[$login];
            if (isset($transferSet[$login])) {
                $target = $this->rotatedGroup($bases, $base);
                $planTransfer[] = [$login, $base, $target];
                $base = $target;
            }

            if ($r['category'] === 3) {
                $planRelink[] = [$login, 'st'.$ticket, $r['groupRaw'], $this->csvFio($r)];
            }

            $staged[] = [
                'ticket' => $ticket,
                'last' => $last,
                'first' => $first,
                'middle' => $middle,
                'group_id' => $groupIds[$base],
            ];
        }

        // ── Зачисления: только в ЛК, в Moodle их нет ──
        $moodleWords = $this->moodleWordSet($rows);
        $planEnroll = [];
        $enrollTicket = self::ENROLL_TICKET_START;
        for ($k = 0; $k < self::N_ENROLL; $k++) {
            [$last, $first, $middle] = $this->pickEnrollFio($moodleWords, $k);
            $ticket = $this->nextFreeTicket($enrollTicket, $csvTickets);
            $base = $bases[$k % count($bases)];
            $planEnroll[] = ['st'.$ticket, "{$last} {$first} {$middle}", $base];
            $staged[] = [
                'ticket' => $ticket,
                'last' => $last,
                'first' => $first,
                'middle' => $middle,
                'group_id' => $groupIds[$base],
            ];
        }

        // ── Массовая запись ──
        $created = $this->bulkCreate($staged, $passwordHash, $now);
        Cache::forget('lk:students');

        $this->command->info("Готово: создано студентов ЛК — {$created}.");
        $this->printPlan($planEnroll, $planExpel, $planFio, $planTransfer, $planRelink);

        $this->command->info('Кеш lk:students очищен.');
        $this->command->line('Дальше: страница Синхронизация с Moodle → загрузить mdl_user (15).csv → «Обработать».');
    }

    // ─── План (вывод) ───────────────────────────────────────────

    private function printPlan(
        array $enroll,
        array $expel,
        array $fio,
        array $transfer,
        array $relink
    ): void {
        $this->command->newLine();
        $this->command->info('── Что покажет сверка ──');

        $this->command->line('Зачисления (есть в ЛК, нет в Moodle): '.count($enroll));
        foreach ($enroll as [$login, $name, $group]) {
            $this->command->line("  {$login}  {$name}  [{$group}]");
        }

        $this->command->line('Отчисления (есть в Moodle, нет в ЛК): '.count($expel));
        foreach ($expel as [$login, $name, $group]) {
            $this->command->line("  {$login}  {$name}  [{$group}]");
        }

        $this->command->line('Смена ФИО: '.count($fio));
        foreach ($fio as [$login, $stLogin, $old, $new]) {
            $this->command->line("  {$login} ({$stLogin}): «{$old}» → «{$new}»");
        }

        $this->command->line('Переводы: '.count($transfer).' (плюс студенты с составной группой в CSV)');
        foreach ($transfer as [$login, $from, $to]) {
            $this->command->line("  {$login}: «{$from} (2026)» → «{$to} (2026)»");
        }

        $this->command->line('Доп. набор (vr → st): '.count($relink));
        foreach ($relink as [$vr, $st, $group, $name]) {
            $this->command->line("  {$vr} → {$st}  {$name}  [{$group}]");
        }
    }

    // ─── Чтение CSV ─────────────────────────────────────────────

    private function csvPath(): string
    {
        $env = env('MOODLE_REAL_CSV');
        if (is_string($env) && $env !== '') {
            return $env;
        }

        $storage = storage_path('app/mdl_user (15).csv');
        if (is_file($storage)) {
            return $storage;
        }

        return base_path('mdl_user (15).csv');
    }

    /**
     * Читает выгрузку Moodle: Категория;Описание;st_ticket;last_name;first_name;email;group_name_fixed.
     *
     * @return array<string, array{login: string, ticket: int|null, category: int, last: string, first: string, email: string, groupRaw: string}>
     */
    private function readCsv(string $path): array
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
        if ($lines === []) {
            return [];
        }

        $delimiter = str_contains($lines[0], ';') ? ';' : ',';
        $header = array_map(
            fn ($h) => mb_strtolower(trim(str_replace("\xEF\xBB\xBF", '', $h))),
            str_getcsv($lines[0], $delimiter, '"', '"')
        );
        $index = array_flip($header);

        $col = function (array $aliases) use ($index): ?int {
            foreach ($aliases as $alias) {
                if (isset($index[$alias])) {
                    return $index[$alias];
                }
            }

            return null;
        };

        $iCategory = $col(['категория', 'category']);
        $iLogin = $col(['st_ticket', 'login', 'username']);
        $iLast = $col(['last_name', 'lastname']);
        $iFirst = $col(['first_name', 'firstname']);
        $iEmail = $col(['email']);
        $iGroup = $col(['group_name_fixed', 'group', 'group_name']);

        if ($iLogin === null || $iLast === null || $iFirst === null || $iGroup === null) {
            $this->command->error('В заголовке CSV нет обязательных колонок (st_ticket/last_name/first_name/group_name_fixed).');

            return [];
        }

        $result = [];
        $skipped = 0;
        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i], $delimiter, '"', '"');
            if (! isset($row[$iLogin]) || ! isset($row[$iLast]) || ! isset($row[$iFirst]) || ! isset($row[$iGroup])) {
                $skipped++;

                continue;
            }

            $login = trim($row[$iLogin]);
            if ($login === '') {
                $skipped++;

                continue;
            }

            $groupRaw = trim($row[$iGroup]);
            $categoryValue = $iCategory !== null && isset($row[$iCategory]) ? (int) trim($row[$iCategory]) : 0;

            $result[$login] = [
                'login' => $login,
                'ticket' => preg_match('/^st(\d+)$/', $login, $m) ? (int) $m[1] : null,
                'category' => $this->resolveCategory($categoryValue, $login, $groupRaw),
                'last' => trim($row[$iLast]),
                'first' => trim($row[$iFirst]),
                'email' => $iEmail !== null && isset($row[$iEmail]) ? trim($row[$iEmail]) : '',
                'groupRaw' => $groupRaw,
            ];
        }

        if ($skipped > 0) {
            $this->command->warn("Пропущено строк CSV: {$skipped}.");
        }

        return $result;
    }

    private function resolveCategory(int $value, string $login, string $group): int
    {
        if (in_array($value, [1, 2, 3], true)) {
            return $value;
        }
        if (str_starts_with(mb_strtolower($login), 'vr')) {
            return 3;
        }

        return trim($group) === '' ? 2 : 1;
    }

    // ─── ФИО ────────────────────────────────────────────────────

    private function csvFio(array $r): string
    {
        return trim($r['last'].' '.$r['first']);
    }

    private function fioWordCount(array $r): int
    {
        return count($this->fioWords($r['last'].' '.$r['first']));
    }

    /** Слова ФИО: нижний регистр, ё→е, дефисы→пробелы (как normalizeFioWords). */
    private function fioWords(string $value): array
    {
        $value = mb_strtolower(trim($value));
        $value = str_replace('ё', 'е', $value);
        $value = str_replace(['-', '‐', '‑', '‒', '–', '—', '―'], ' ', $value);
        $words = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);

        return $words === false ? [] : array_values($words);
    }

    /**
     * Разложить строку Moodle (last_name|first_name) в [фамилия, имя, отчество] ЛК.
     * Набор слов сохраняется — сверка увидит совпадение при любой раскладке.
     */
    private function splitFio(array $r): array
    {
        $lastWords = preg_split('/\s+/u', trim($r['last']), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $firstWords = preg_split('/\s+/u', trim($r['first']), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (count($lastWords) >= 2 && count($firstWords) === 1) {
            // Стандартная раскладка: «Имя Отчество»|Фамилия.
            return [$firstWords[0], $lastWords[0], implode(' ', array_slice($lastWords, 1))];
        }

        // Перевёрнутая раскладка (и общий случай): Фамилия|«Имя Отчество».
        return [$r['last'], $firstWords[0] ?? '', implode(' ', array_slice($firstWords, 1))];
    }

    /** Вариант для «смены ФИО»: в ЛК добавляется отчество, которого нет в Moodle. */
    private function fioVariant(array $r): array
    {
        [$last, $first] = $this->splitFio($r);

        return [$last, $first, $this->pickPatronymic($first, $last)];
    }

    private function isFemale(string $first, string $last): bool
    {
        $firstLower = mb_strtolower(trim($first));
        $lastLower = mb_strtolower(trim($last));

        // Пол определяем по имени: женские имена оканчиваются на «а/я»,
        // кроме мужских (Никита, Илья, Данила и т.п.).
        if ($firstLower !== '') {
            static $maleNames = [
                'никита', 'илья', 'данила', 'кузьма', 'фома', 'савва', 'лука',
                'гоша', 'миша', 'саша', 'женя', 'валя', 'шура', 'дима', 'коля',
            ];
            if (preg_match('/(а|я)$/u', $firstLower) && ! in_array($firstLower, $maleNames, true)) {
                return true;
            }
            if (preg_match('/[бвгджзйклмнпрстфхцчшщ]$/u', $firstLower)) {
                return false;
            }
        }

        // Фолбэк по фамилии.
        if (preg_match('/(ова|ева|ина|ская|цкая|ая|яя)$/u', $lastLower)) {
            return true;
        }
        if (preg_match('/(ов|ев|ин|ский|цкий|ый|ий)$/u', $lastLower)) {
            return false;
        }

        return (bool) preg_match('/(а|я)$/u', $lastLower);
    }

    private function pickPatronymic(string $first, string $last): string
    {
        static $middlesM = [
            'Иванович', 'Петрович', 'Алексеевич', 'Сергеевич', 'Дмитриевич',
            'Олегович', 'Владимирович', 'Николаевич', 'Андреевич', 'Фёдорович',
        ];
        static $middlesF = [
            'Ивановна', 'Петровна', 'Алексеевна', 'Сергеевна', 'Дмитриевна',
            'Олеговна', 'Владимировна', 'Николаевна', 'Андреевна', 'Фёдоровна',
        ];
        $pool = $this->isFemale($first, $last) ? $middlesF : $middlesM;

        return $pool[mt_rand(0, count($pool) - 1)];
    }

    /**
     * ФИО для зачисления. Фамилия гарантированно отсутствует в Moodle,
     * иначе сверка сопоставила бы «нового» студента с чужой строкой.
     */
    private function pickEnrollFio(array $moodleWords, int $k): array
    {
        $base = self::ENROLL_NAMES[$k % count(self::ENROLL_NAMES)];
        [$last, $first, $middle] = $base;

        if (isset($moodleWords[mb_strtolower($last)])) {
            $last .= 'нов';
        }
        if (isset($moodleWords[mb_strtolower($last)])) {
            $last .= 'ский';
        }

        return [$last, $first, $middle];
    }

    /** @return array<string, bool> */
    private function moodleWordSet(array $rows): array
    {
        $set = [];
        foreach ($rows as $r) {
            foreach ($this->fioWords($r['last'].' '.$r['first']) as $word) {
                $set[$word] = true;
            }
        }

        return $set;
    }

    // ─── Группы ─────────────────────────────────────────────────

    /**
     * Название группы для БД ЛК.
     *
     * SQL getLkStudents сам приводит группу к виду «ИМЯ (текущий год)»,
     * поэтому в БД храним имя без суффикса « (год)». У составных названий
     * («A (2026), B (2026)») берём первую группу: в ЛК студент состоит
     * в одной группе.
     */
    private function groupBase(string $raw): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return '';
        }

        $first = trim(explode(',', $raw)[0]);

        return trim(preg_replace('/\s*\(\d{4}\)\s*$/u', '', $first) ?? $first);
    }

    private function ensureGroup(string $name, int $externalId): int
    {
        $admissionYear = 2024;
        if (preg_match('/-(\d{2})\b/u', $name, $m)) {
            $admissionYear = 2000 + (int) $m[1];
        }

        $group = Group::firstOrCreate(
            ['group_name' => $name],
            [
                'course' => 1,
                'year_admission' => sprintf('%d-09-01', $admissionYear),
                'year_completion' => sprintf('%d-06-30', $admissionYear + 4),
                'study_plan_id' => 1,
                'work_study_plan_id' => 1,
                'branch_id' => 1,
                'external_id' => $externalId,
            ]
        );

        return $group->id;
    }

    private function rotatedGroup(array $names, string $current): string
    {
        $i = array_search($current, $names, true);
        if ($i === false) {
            return $names[0];
        }

        return $names[($i + 1) % count($names)];
    }

    // ─── Служебное ──────────────────────────────────────────────

    private function isStLogin(string $login): bool
    {
        return (bool) preg_match('/^st\d+$/', $login);
    }

    /**
     * Свободный билет из диапазона; занятые (в т.ч. из CSV) пропускаются.
     */
    private function nextFreeTicket(int &$cursor, array &$taken): int
    {
        while (isset($taken[$cursor])) {
            $cursor++;
        }
        $ticket = $cursor;
        $taken[$ticket] = true;
        $cursor++;

        return $ticket;
    }

    // ─── Запись в БД ────────────────────────────────────────────

    /**
     * @param  array<int, array{ticket: int, last: string, first: string, middle: string, group_id: int}>  $staged
     */
    private function bulkCreate(array $staged, string $passwordHash, string $now): int
    {
        $created = 0;
        $bar = $this->command->getOutput()->createProgressBar((int) ceil(count($staged) / self::CHUNK));

        foreach (array_chunk($staged, self::CHUNK) as $chunk) {
            $userRows = [];
            foreach ($chunk as $s) {
                $userRows[] = [
                    'name' => 'st'.$s['ticket'],
                    'email' => 'st'.$s['ticket'].'@'.self::EMAIL_DOMAIN,
                    'password' => $passwordHash,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $maxBefore = (int) (DB::table('users')->max('id') ?? 0);
            DB::table('users')->insert($userRows);
            $ids = DB::table('users')
                ->where('id', '>', $maxBefore)
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $dataRows = [];
            $ticketRows = [];
            $linkRows = [];
            foreach ($ids as $k => $userId) {
                $s = $chunk[$k];
                $dataRows[] = array_merge(
                    ['user_id' => $userId, 'external_user_data_id' => $userId, 'external_user_id' => $userId],
                    $this->usersDataValues($s['last'], $s['first'], $s['middle'], $s['ticket'], $now)
                );
                $ticketRows[] = [
                    'user_id' => $userId,
                    'ticket_number' => $s['ticket'],
                    'external_id' => $s['ticket'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $linkRows[] = [
                    'group_id' => $s['group_id'],
                    'user_id' => $userId,
                    'external_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('users_data')->insert($dataRows);
            DB::table('student_ticket_numbers')->insert($ticketRows);
            DB::table('group_student')->insert($linkRows);

            $created += count($chunk);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        return $created;
    }

    /**
     * Значения users_data (тот же набор колонок, что в ContingentLoadSeeder).
     *
     * @return array<string, mixed>
     */
    private function usersDataValues(string $last, string $first, string $middle, int $ticket, string $now): array
    {
        return [
            'last_name' => $last,
            'first_name' => $first,
            'middle_name' => $middle,
            'birthday' => '2003-04-17',
            'birthplace' => 'г. Ростов-на-Дону',
            'sex' => $this->isFemale($first, $last) ? 0 : 1,
            'document_id' => 1,
            'passport_series' => '6001',
            'passport_number' => (string) (1000000 + ($ticket % 900000)),
            'passport_issue_date' => '2020-02-02',
            'passport_issuing_authority' => 'ГУ МВД',
            'division_code' => '610-002',
            'snils' => '111-111-111 11',
            'inn' => '616400000001',
            'family_status_id' => 1,
            'is_residence' => true,
            'citizenship' => 1,
            'country' => 155,
            'last_name_genitive' => $last,
            'first_name_genitive' => $first,
            'middle_name_genitive' => $middle,
            'last_name_dative' => $last,
            'first_name_dative' => $first,
            'middle_name_dative' => $middle,
            'branch_id' => 1,
            'is_deleted' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }
}
