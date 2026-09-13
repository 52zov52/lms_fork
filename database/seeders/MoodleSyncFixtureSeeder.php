<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Фикстура ЛК под файл moodle_3000.csv для вкладки
 * «Управление контингентом → Синхронизация с Moodle».
 *
 * Перестраивает студентов ЛК так, чтобы сверка с CSV давала
 * реалистичное распределение (как в настоящем колледже):
 *   - совпадения (без изменений) — большинство;
 *   - смена ФИО (в ЛК есть отчество / исправленное ФИО, в Moodle нет);
 *   - переводы (в ЛК другая группа, чем в Moodle);
 *   - зачисления (есть в ЛК, нет в Moodle);
 *   - отчисления (есть в Moodle, нет в ЛК).
 *
 * Распределение (детерминировано, seed=42):
 *   exact=2500, fio=50, transfer=50, expel-skip=123, enroll-new=50
 *   → студентов в ЛК: 2700.
 *
 * Что делает:
 *   1. Читает CSV (st_ticket,last_name,first_name,email,group_name_fixed).
 *   2. Удаляет старых нагрузочных студентов (email *@load.test / *@fixture.test).
 *   3. Демо-аккаунты (student1..6, билеты 100001–100006) НЕ удаляет, а
 *      «усыновляет»: обновляет их ФИО/группу под строки CSV.
 *   4. Создаёт остальных студентов из строк CSV (билет = st_ticket без «st»).
 *   5. Чистит кеш lk:students (иначе вкладка 30 минут показывает старое).
 *
 * Запуск:
 *   php artisan db:seed --class=MoodleSyncFixtureSeeder
 *
 * Сидер идемпотентен по результату: повторный запуск сначала стирает
 * свою же фикстуру и строит её заново с тем же распределением.
 */
class MoodleSyncFixtureSeeder extends Seeder
{
    private const CHUNK = 500;
    private const SEED = 42;
    private const N_FIO = 50;
    private const N_TRANSFER = 50;
    private const N_ENROLL = 50;
    private const DB_TARGET = 2700;
    private const ENROLL_TICKET_START = 950001;
    private const EMAIL_DOMAIN = 'fixture.test';

    public function run(): void
    {
        // Справочники/планы/роли — нужны для групп и аккаунтов.
        $this->call(ContingentSeeder::class);

        // Сброс билетов демо-аккаунтов: прошлый прогон мог их погасить (см. ниже),
        // а ContingentSeeder deleted_at не чистит. Иначе усыновление не сработает.
        $now0 = Carbon::now()->toDateTimeString();
        foreach ([
            'student1@i.ru' => 100001, 'student2@i.ru' => 100002, 'student3@i.ru' => 100003,
            'student4@i.ru' => 100004, 'student5@i.ru' => 100005, 'student6@i.ru' => 100006,
        ] as $email => $ticket) {
            $uid = DB::table('users')->where('email', $email)->value('id');
            if ($uid) {
                DB::table('student_ticket_numbers')->updateOrInsert(
                    ['user_id' => $uid],
                    ['ticket_number' => $ticket, 'external_id' => $ticket, 'deleted_at' => null, 'updated_at' => $now0]
                );
            }
        }

        $csvPath = storage_path('app/moodle_3000.csv');
        if (! is_file($csvPath)) {
            $csvPath = base_path('moodle_3000.csv');
        }
        if (! is_file($csvPath)) {
            $this->command->error('CSV не найден: storage/app/moodle_3000.csv или ./moodle_3000.csv');
            return;
        }

        $rows = $this->readCsv($csvPath);
        $this->command->info('Строк CSV: ' . count($rows));
        if (empty($rows)) {
            return;
        }

        // ── Корзины строк (детерминированно) ──
        mt_srand(self::SEED);
        $idx = array_keys($rows);
        shuffle($idx);

        // ФИО: только строки без отчества (2 слова) — в ЛК добавим отчество.
        $twoWord = array_values(array_filter($idx, fn ($i) => $rows[$i]['words'] === 2));
        $fioIdx = array_slice($twoWord, 0, self::N_FIO);
        $fioSet = array_flip($fioIdx);

        $rest = array_values(array_filter($idx, fn ($i) => ! isset($fioSet[$i])));
        $transferIdx = array_slice($rest, 0, self::N_TRANSFER);
        $transferSet = array_flip($transferIdx);

        $rest2 = array_values(array_filter($rest, fn ($i) => ! isset($transferSet[$i])));
        // Сколько строк CSV пропустить (отчисления), чтобы в ЛК вышло DB_TARGET:
        // DB = (rows - skip) + enroll  →  skip = rows + enroll - target.
        $nSkip = count($rows) + self::N_ENROLL - self::DB_TARGET;
        if ($nSkip < 0) {
            $nSkip = 0;
        }
        $skipIdx = array_slice($rest2, 0, $nSkip);
        $skipSet = array_flip($skipIdx);

        $nFio = count($fioIdx);
        $nTransfer = count($transferIdx);
        $nSkipC = count($skipIdx);
        $nExact = count($rows) - $nFio - $nTransfer - $nSkipC;
        $this->command->info("План: exact={$nExact} fio={$nFio} transfer={$nTransfer} expel-skip={$nSkipC} enroll-new=" . self::N_ENROLL);

        // ── Снос старой нагрузки ──
        $wipeIds = DB::table('users')
            ->where('email', 'like', '%@load.test')
            ->orWhere('email', 'like', '%@fixture.test')
            ->pluck('id')->all();
        if (! empty($wipeIds)) {
            foreach (array_chunk($wipeIds, 1000) as $chunkIds) {
                DB::table('group_student')->whereIn('user_id', $chunkIds)->delete();
                DB::table('users_data')->whereIn('user_id', $chunkIds)->delete();
                DB::table('student_ticket_numbers')->whereIn('user_id', $chunkIds)->delete();
                DB::table('users')->whereIn('id', $chunkIds)->delete();
            }
            $this->command->info('Удалено нагрузочных пользователей: ' . count($wipeIds));
        }

        // Защита от дублей логина st_ticket: один билет — один живой номер.
        $dups = DB::select('SELECT ticket_number FROM student_ticket_numbers WHERE deleted_at IS NULL GROUP BY ticket_number HAVING COUNT(*) > 1');
        foreach ($dups as $d) {
            $ids = DB::table('student_ticket_numbers')
                ->whereNull('deleted_at')->where('ticket_number', $d->ticket_number)
                ->orderBy('id')->pluck('id')->all();
            array_shift($ids);
            if (! empty($ids)) {
                DB::table('student_ticket_numbers')->whereIn('id', $ids)
                    ->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
            }
        }

        // ── Группы: все имена из CSV должны существовать ──
        $groupNames = array_values(array_unique(array_column($rows, 'group')));
        sort($groupNames);
        $groupIds = [];
        foreach ($groupNames as $gi => $gname) {
            $groupIds[$gname] = $this->ensureGroup($gname, 920000 + $gi);
        }
        $this->command->info('Групп задействовано: ' . count($groupIds));

        // Карта билет → user_id (после сноса тут только демо-аккаунты).
        $ticketToUser = DB::table('student_ticket_numbers')
            ->whereNull('deleted_at')->pluck('user_id', 'ticket_number')->all();

        $now = Carbon::now()->toDateTimeString();
        $passwordHash = Hash::make('password');

        // ── Назначение строкам ФИО и групп ──
        $staged = [];   // строки на создание
        $adopted = 0;
        foreach ($rows as $i => $r) {
            if (isset($skipSet[$i])) {
                continue; // отчисление: студента нет в ЛК
            }
            if (isset($fioSet[$i])) {
                [$last, $first, $middle] = $this->fioVariant($r);
            } else {
                [$last, $first, $middle] = $this->splitFio($r);
            }
            $groupName = isset($transferSet[$i])
                ? $this->rotatedGroup($groupNames, $r['group'])
                : $r['group'];

            $ticket = $r['ticket'];
            if (isset($ticketToUser[$ticket])) {
                // Демо-аккаунт с таким билетом: обновляем данные под CSV.
                $this->adoptUser((int) $ticketToUser[$ticket], $ticket, $last, $first, $middle, $groupIds[$groupName], $now);
                $adopted++;
                unset($ticketToUser[$ticket]);
            } else {
                $staged[] = [
                    'ticket' => $ticket, 'last' => $last, 'first' => $first,
                    'middle' => $middle, 'group_id' => $groupIds[$groupName],
                ];
            }
        }

        // ── Зачисления: студенты только в ЛК (билетов нет в CSV) ──
        mt_srand(self::SEED + 1);
        $ticket = self::ENROLL_TICKET_START;
        $usedTickets = array_flip(array_column($rows, 'ticket'));
        for ($k = 0; $k < self::N_ENROLL; $k++) {
            while (isset($usedTickets[$ticket]) || isset($ticketToUser[$ticket])) {
                $ticket++;
            }
            [$last, $first, $middle] = $this->randomFio();
            $staged[] = [
                'ticket' => $ticket, 'last' => $last, 'first' => $first,
                'middle' => $middle,
                'group_id' => $groupIds[$groupNames[$k % count($groupNames)]],
            ];
            $usedTickets[$ticket] = true;
            $ticket++;
        }

        // ── Пропущенные строки: если билет держит демо-аккаунт — гасим билет
        // и связь с группой, чтобы строка CSV стала честным «отчислением»
        // (иначе демо-данные дадут ложное ФИО/совпадение вместо отчисления).
        $expelFixed = 0;
        foreach ($skipIdx as $i) {
            $uid = DB::table('student_ticket_numbers')
                ->whereNull('deleted_at')->where('ticket_number', $rows[$i]['ticket'])
                ->value('user_id');
            if ($uid) {
                DB::table('student_ticket_numbers')->where('user_id', $uid)->whereNull('deleted_at')
                    ->update(['deleted_at' => $now, 'updated_at' => $now]);
                DB::table('group_student')->where('user_id', $uid)->delete();
                $expelFixed++;
            }
        }
        if ($expelFixed > 0) {
            $this->command->info("Погашено демо-билетов под отчисления: {$expelFixed}.");
        }

        // ── Массовое создание ──
        $created = $this->bulkCreate($staged, $passwordHash, $now);

        Cache::forget('lk:students');

        $total = $adopted + $created;
        $this->command->info("Готово: усыновлено демо — {$adopted}, создано — {$created}, всего студентов фикстуры — {$total}.");
        $this->command->info('Ожидаемая сверка с moodle_3000.csv: совпадения ~' . $nExact
            . ', ФИО ~' . $nFio . ', переводы ~' . $nTransfer
            . ', отчисления ~' . $nSkipC . ', зачисления ~' . self::N_ENROLL . '.');
        $this->command->info('Кеш lk:students очищен. В браузере: загрузить CSV → «Обработать».');
    }

    // ─── Чтение CSV ───

    /**
     * @return array<int, array{login: string, ticket: int, last: string, first: string, email: string, group: string, words: int}>
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
        if (empty($lines)) {
            return [];
        }
        $delimiter = str_contains($lines[0], ';') ? ';' : ',';
        $rows = [];
        $bad = 0;
        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i], $delimiter, '"', '"');
            if (count($row) < 5) {
                $bad++;
                continue;
            }
            [$login, $last, $first, $email, $group] = array_map('trim', array_slice($row, 0, 5));
            if (! preg_match('/^st(\d+)$/', $login, $m)) {
                $bad++;
                continue;
            }
            $rows[] = [
                'login' => $login,
                'ticket' => (int) $m[1],
                'last' => $last,
                'first' => $first,
                'email' => $email,
                'group' => $group,
                'words' => count($this->fioWords($last . ' ' . $first)),
            ];
        }
        if ($bad > 0) {
            $this->command->warn("Пропущено строк CSV: {$bad}.");
        }

        return $rows;
    }

    // ─── ФИО ───

    /** Слова ФИО: нижний регистр, ё→е, дефисы→пробелы (как normalizeFioWords в компоненте). */
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
     * Набор слов сохраняется — сверка-компонент увидит совпадение при любой
     * раскладке Moodle (перевёрнутой или стандартной).
     *
     * @param  array{last: string, first: string}  $r
     * @return array{0: string, 1: string, 2: string}
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

    /**
     * Вариант для корзины «смена ФИО»: те же фамилия+имя, но в ЛК есть
     * отчество, которого нет в Moodle (реальный кейс потери отчества).
     *
     * @param  array{last: string, first: string}  $r
     * @return array{0: string, 1: string, 2: string}
     */
    private function fioVariant(array $r): array
    {
        [$last, $first] = $this->splitFio($r);

        return [$last, $first, $this->pickPatronymic($first, $last)];
    }

    /** Пол по имени/фамилии: true — женский. */
    private function isFemale(string $first, string $last): bool
    {
        static $firstsF = [
            'Анна', 'Елена', 'Ольга', 'Мария', 'Наталья', 'Екатерина', 'Алёна',
            'Дарья', 'Полина', 'Софья', 'Виктория', 'Анастасия', 'Вера', 'Надежда', 'Любовь',
        ];
        static $firstsM = [
            'Иван', 'Пётр', 'Алексей', 'Максим', 'Дмитрий', 'Сергей', 'Андрей',
            'Олег', 'Владимир', 'Николай', 'Артём', 'Даниил', 'Егор', 'Илья',
            'Роман', 'Тимур',
        ];
        if (in_array($first, $firstsF, true)) {
            return true;
        }
        if (in_array($first, $firstsM, true)) {
            return false;
        }

        return (bool) preg_match('/(а|я)$/u', mb_strtolower(trim($last)));
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

    /** Случайное ФИО для корзины «зачисления» (только в ЛК). */
    private function randomFio(): array
    {
        static $lasts = [
            'Иванов', 'Петров', 'Сидоров', 'Смирнов', 'Кузнецов', 'Попов', 'Соколов',
            'Михайлов', 'Фёдоров', 'Семёнов', 'Павлов', 'Козлов', 'Морозов', 'Новиков',
            'Волков', 'Лебедев', 'Соловьёв', 'Богданов', 'Воробьёв', 'Андреев',
            'Салтыков-Щедрин', 'Петров-Водкин', 'Соколов-Микитов',
            'Иванова', 'Петрова', 'Смирнова', 'Кузнецова', 'Попова', 'Соколова',
            'Фёдорова', 'Морозова', 'Новикова', 'Волкова', 'Лебедева', 'Соловьёва',
            'Захаров', 'Ершов', 'Григорьев', 'Тихонов', 'Лазарев',
            'Матвеева', 'Родионова',
        ];
        static $firstsM = [
            'Иван', 'Пётр', 'Алексей', 'Максим', 'Дмитрий', 'Сергей', 'Андрей',
            'Олег', 'Владимир', 'Николай', 'Артём', 'Даниил', 'Егор', 'Илья',
        ];
        static $firstsF = [
            'Анна', 'Елена', 'Ольга', 'Мария', 'Наталья', 'Екатерина', 'Алёна',
            'Дарья', 'Полина', 'Софья', 'Виктория', 'Анастасия',
        ];
        static $middlesM = [
            'Иванович', 'Петрович', 'Алексеевич', 'Сергеевич', 'Дмитриевич',
            'Олегович', 'Владимирович', 'Николаевич', 'Андреевич', 'Фёдорович',
        ];
        static $middlesF = [
            'Ивановна', 'Петровна', 'Алексеевна', 'Сергеевна', 'Дмитриевна',
            'Олеговна', 'Владимировна', 'Николаевна', 'Андреевна', 'Фёдоровна',
        ];

        $female = (bool) mt_rand(0, 1);
        $last = $lasts[mt_rand(0, count($lasts) - 1)];
        // Пол подгоняем под фамилию, чтобы данные выглядели как в колледже.
        if (preg_match('/(а|я)$/u', mb_strtolower($last))) {
            $female = true;
        } elseif (preg_match('/(ов|ев|ин)$/u', mb_strtolower($last))) {
            $female = false;
        }
        $first = $female
            ? $firstsF[mt_rand(0, count($firstsF) - 1)]
            : $firstsM[mt_rand(0, count($firstsM) - 1)];
        $middle = $female
            ? $middlesF[mt_rand(0, count($middlesF) - 1)]
            : $middlesM[mt_rand(0, count($middlesM) - 1)];

        return [$last, $first, $middle];
    }

    // ─── Группы ───

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

    /** Следующая группа по кругу (гарантированно отличается). */
    private function rotatedGroup(array $names, string $current): string
    {
        $i = array_search($current, $names, true);
        if ($i === false) {
            return $names[0];
        }

        return $names[($i + 1) % count($names)];
    }

    // ─── Запись в БД ───

    /**
     * Обновить существующего пользователя (демо-аккаунт) под строку CSV.
     * Аккаунт для входа сохраняется, меняются только данные и группа.
     */
    private function adoptUser(int $userId, int $ticket, string $last, string $first, string $middle, int $groupId, string $now): void
    {
        DB::table('users_data')->updateOrInsert(
            ['user_id' => $userId],
            $this->usersDataValues($last, $first, $middle, $ticket, $now)
        );
        DB::table('student_ticket_numbers')
            ->where('user_id', $userId)->whereNull('deleted_at')
            ->update(['ticket_number' => $ticket, 'external_id' => $ticket, 'updated_at' => $now]);
        DB::table('group_student')->where('user_id', $userId)->delete();
        DB::table('group_student')->insert([
            'group_id' => $groupId,
            'user_id' => $userId,
            'external_id' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    /**
     * Массовое создание студентов чанками (users → users_data/tickets/links).
     *
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
                    'name' => 'st' . $s['ticket'],
                    'email' => 'st' . $s['ticket'] . '@' . self::EMAIL_DOMAIN,
                    'password' => $passwordHash,
                    'email_verified_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('users')->insert($userRows);
            $ids = DB::table('users')
                ->where('email', 'like', '%@' . self::EMAIL_DOMAIN)
                ->orderByDesc('id')
                ->limit(count($chunk))
                ->pluck('id')
                ->reverse()
                ->values()
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
            // Уникальный в пределах фикстуры: билеты не повторяются,
            // а разброс билетов (100001–950050) меньше 900000.
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
