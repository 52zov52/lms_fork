<?php

namespace App\Livewire\Employee;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Управление контингентом: сравнение данных ЛК и Moodle.
 *
 * Пайплайн:
 *   1. Получить список студентов из БД ЛК (кеш 30 мин).
 *   2. Загрузить CSV-файл с данными Moodle.
 *   3. Сравнить по логину → определить зачисления, отчисления, смены ФИО, переводы.
 *   4. Сгенерировать SQL-запрос (Блоки 1–5) для приведения Moodle в соответствие с ЛК.
 *
 * SQL-блоки:
 *   1 — создание пользователей (INSERT mdl_user)
 *   2 — обновление ФИО (UPDATE mdl_user)
 *   3 — удаление из старых когорт (DELETE mdl_cohort_members)
 *   4 — добавление в новые когорт (INSERT mdl_cohort_members)
 *   5 — отчисление (DELETE + UPDATE mdl_user SET deleted=1)
 */
class MoodleContingentSync extends Component
{
    use WithFileUploads;

    // ─── Состояние Livewire (публичные свойства) ────────────────

    /** Загружаемый CSV-файл Moodle (Livewire temporary upload). */
    public ?TemporaryUploadedFile $csvFile = null;

    /** Флаг: CSV обработан, данные сравнены, SQL сгенерирован. */
    public bool $parsed = false;

    /** Список найденных расхождений (массив ассоциативных массивов). */
    public array $changes = [];

    /**
     * Состояние чекбоксов: selected[id] = true/false.
     * Определяет, какие изменения попадут в генерируемый SQL.
     */
    public array $selected = [];

    /** Счётчики по типам: enroll (зачисление), expel (отчисление), fio (ФИО), transfer (перевод). */
    public array $stats = [
        'enroll' => 0,
        'expel'  => 0,
        'fio'    => 0,
        'transfer' => 0,
    ];

    /** Сгенерированный SQL-запрос для Moodle (отображается в предпросмотре). */
    public string $sql = '';

    /** SQL-запрос для выборки студентов из Moodle (скопировать в Step 1). */
    public string $moodleQuery = '';

    /** Предупреждения при парсинге CSV (дубли, пропущенные строки). */
    public array $csvWarnings = [];

    // ─── Жизненный цикл Livewire ────────────────────────────────

    /**
     * Инициализация компонента: загрузка SQL-запроса Moodle в кеш.
     */
    public function mount(): void
    {
        $this->moodleQuery = Cache::remember(
            'lk:moodle_query',
            now()->addHours(24),
            fn () => $this->getMoodleQuery()
        );
    }

    /**
     * Рендер представления.
     * При наличии обработанных данных — пересчитывает SQL для актуальности чекбоксов.
     */
    public function render()
    {
        if ($this->parsed) {
            $this->sql = $this->buildSql();
        }

        return view('livewire.employee.moodle-contingent-sync');
    }

    // ─── Публичные действия (Livewire wire:click) ───────────────

    /**
     * Обработка CSV-файла Moodle: парсинг → сравнение с ЛК → формирование списка изменений.
     *
     * Вызывается по кнопке «Обработать» в Section 2.
     */
    public function process(): void
    {
        $this->resetValidation();
        $this->reset('changes', 'selected', 'stats', 'sql', 'parsed');
        $this->stats = ['enroll' => 0, 'expel' => 0, 'fio' => 0, 'transfer' => 0];

        if (! $this->csvFile) {
            $this->addError('csvFile', 'Загрузите CSV-файл с данными Мудл.');
            return;
        }

        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        try {
            $moodle = $this->parseMoodleCsv($this->csvFile->getRealPath());
        } catch (Exception $e) {
            $this->addError('csvFile', 'Не удалось разобрать CSV Мудл: ' . $e->getMessage());
            return;
        }

        try {
            $lk = $this->getLkStudents();
        } catch (Exception $e) {
            Cache::forget('lk:students');
            $lk = [];
            $this->dispatch(
                'show-toast',
                message: 'Данные ЛК из БД недоступны — сравнение выполнено только по файлу Мудл (все студенты помечены как «отчисление»).',
                type: 'warning'
            );
        }

        $this->compare($lk, $moodle);

        $this->parsed = true;
        $this->dispatch('show-toast', message: 'Сравнение выполнено. Найдено изменений: ' . count($this->changes), type: 'success');
    }

    /**
     * Скачивание сгенерированного SQL-запроса в .txt-файл.
     */
    public function downloadSql()
    {
        if (empty($this->changes)) {
            $this->dispatch('show-toast', message: 'Нет данных для выгрузки. Сначала загрузите файл.', type: 'error');
            return;
        }

        $sql = $this->buildSql();

        return Response::streamDownload(function () use ($sql) {
            echo $sql;
        }, 'moodle_contingent_changes.txt');
    }

    // ─── Парсинг CSV ────────────────────────────────────────────

    /**
     * Парсинг CSV-файла Moodle.
     *
     * Поддерживает:
     *   - разделители `,` и `;` (автоопределение)
     *   - кодировки UTF-8, windows-1251, ISO-8859-1, ASCII (автоконвертация)
     *   - дубли логинов (предупреждение, используется последняя запись)
     *   - пустые строки (пропуск + подсчёт)
     *
     * Ожидаемые колонки (регистронезависимо, на русском или английском):
     *   - login:      st_ticket, username, логин, login
     *   - last_name:  last_name, фамилия, surname
     *   - first_name: first_name, имя, name
     *   - email:      email
     *   - group:      group_name_fixed, group, группа, реальный_группа, real_group
     *
     * @param  string  $path  Абсолютный путь к CSV-файлу (временный файл Livewire)
     *
     * @return array<string, array{login: string, last_name: string, first_name: string, email: string, group: string}>
     *         Ассоциативный массив [login => data], ключ — логин студента.
     *
     * @throws Exception  Если файл пуст или отсутствуют обязательные колонки.
     */
    private function parseMoodleCsv(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new Exception('Файл недоступен.');
        }

        // ── Конвертация кодировки ──
        $encoding = mb_detect_encoding($content, ['UTF-8', 'windows-1251', 'ISO-8859-1', 'ASCII'], true);
        if ($encoding !== false && $encoding !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }

        // ── Разбиение на строки ──
        $lines = preg_split('/\r\n|\r|\n/', $content);
        $lines = array_filter($lines, fn ($l) => trim($l) !== '');
        if (empty($lines)) {
            throw new Exception('Файл пуст.');
        }

        // ── Определение разделителя и заголовков ──
        $delimiter = str_contains($lines[0], ';') ? ';' : ',';
        $header = array_map(fn ($h) => mb_strtolower(trim($h)), str_getcsv($lines[0], $delimiter, '"', '"'));

        // ── Маппинг колонок ──
        $map = [];
        foreach ($header as $i => $h) {
            if (in_array($h, ['st_ticket', 'username', 'логин', 'login'])) {
                $map['login'] = $i;
            } elseif (in_array($h, ['last_name', 'фамилия', 'surname'])) {
                $map['last_name'] = $i;
            } elseif (in_array($h, ['first_name', 'имя', 'name'])) {
                $map['first_name'] = $i;
            } elseif ($h === 'email') {
                $map['email'] = $i;
            } elseif (in_array($h, ['group_name_fixed', 'group', 'группа', 'реальный_группа', 'real_group'])) {
                $map['group'] = $i;
            }
        }

        // ── Валидация обязательных колонок ──
        foreach (['login', 'last_name', 'first_name', 'group'] as $required) {
            if (! isset($map[$required])) {
                throw new Exception("В заголовке CSV не найдена колонка '$required'.");
            }
        }

        // ── Парсинг строк ──
        $result = [];
        $skippedRows = 0;
        $duplicateLogins = [];
        $seenLogins = [];

        for ($n = 1; $n < count($lines); $n++) {
            $row = str_getcsv($lines[$n], $delimiter, '"', '"');
            if (count($row) <= max($map['login'], $map['last_name'], $map['first_name'], $map['group'])) {
                $skippedRows++;
                continue;
            }

            $login = trim($row[$map['login']]);
            if ($login === '') {
                $skippedRows++;
                continue;
            }

            if (isset($seenLogins[$login])) {
                $duplicateLogins[$login] = ($duplicateLogins[$login] ?? 1) + 1;
            }
            $seenLogins[$login] = true;

            $result[$login] = [
                'login'      => $login,
                'last_name'  => trim($row[$map['last_name']]),
                'first_name' => trim($row[$map['first_name']]),
                'email'      => isset($map['email']) ? trim($row[$map['email']]) : '',
                'group'      => trim($row[$map['group']]),
            ];
        }

        // ── Формирование предупреждений ──
        $this->csvWarnings = [];
        if ($skippedRows > 0) {
            $this->csvWarnings[] = "Пропущено строк: $skippedRows (пустой логин или некорректный формат).";
        }
        if (! empty($duplicateLogins)) {
            foreach ($duplicateLogins as $dupLogin => $count) {
                $this->csvWarnings[] = "Дубль логина \"$dupLogin\": $count записей, используется последняя.";
            }
        }

        return $result;
    }

    // ─── Запрос к БД ЛК ────────────────────────────────────────

    /**
     * Получение списка студентов из базы данных ЛК (MySQL).
     *
     * Запрос выбирает активных студентов из действующих групп,
     * где год окончания >= текущей даты, год поступления не старше 6 лет.
     *
     * Результат кешируется на 30 минут (ключ: lk:students).
     *
     * @return array<string, array{login: string, last_name: string, first_name: string, email: string, group: string}>
     */
    private function getLkStudents(): array
    {
        return Cache::remember('lk:students', now()->addMinutes(30), function () {
            $sql = "
                SELECT
                    CONCAT('st', sub.latest_ticket) AS st_ticket,
                    sub.last_name AS last_name,
                    sub.first_name AS first_name,
                    CONCAT_WS(' ', sub.first_name, sub.middle_name) AS full_name,
                    CONCAT(sub.latest_ticket, '_', YEAR(sub.year_admission), '@iubip.ru') AS email,
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
                        INNER JOIN groups g ON gs.group_id = g.id AND g.deleted_at IS NULL
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
            ";

            $rows = DB::select($sql);

            $result = [];
            foreach ($rows as $row) {
                $login = $row->st_ticket;
                $result[$login] = [
                    'login'      => $login,
                    'last_name'  => $row->last_name,
                    'first_name' => $row->first_name,
                    'email'      => $row->email,
                    'group'      => $row->group_name_fixed,
                ];
            }

            return $result;
        });
    }

    // ─── Сравнение данных ──────────────────────────────────────

    /**
     * Сравнение данных ЛК и Moodle, формирование списка изменений.
     *
     * Логика по логину:
     *   - Есть в ЛК, нет в Moodle → enroll (зачисление)
     *   - Есть в Moodle, нет в ЛК  → expel  (отчисление)
     *   - Есть в обоих, различается ФИО → fio
     *   - Есть в обоих, различается группа → transfer (перевод)
     *
     * Сравнение ФИО/групп — регистронезависимое (mb_strtolower).
     *
     * @param  array  $lk       Данные ЛК (login => data)
     * @param  array  $moodle   Данные Moodle (login => data)
     */
    private function compare(array $lk, array $moodle): void
    {
        $lkLogins = array_keys($lk);
        $moodleLogins = array_keys($moodle);

        $onlyLk = array_diff($lkLogins, $moodleLogins);
        $onlyMoodle = array_diff($moodleLogins, $lkLogins);
        $both = array_intersect($lkLogins, $moodleLogins);

        // ── Зачисление: есть в ЛК, нет в Moodle ──
        foreach ($onlyLk as $login) {
            $targetGroup = $this->targetGroup($lk[$login]['group']);
            $this->addChange([
                'type'       => 'enroll',
                'reason'     => 'Есть в ЛК, нет в Мудл',
                'login'      => $login,
                'surnameOld' => '',
                'surnameNew' => $lk[$login]['last_name'],
                'nameOld'    => '',
                'nameNew'    => $lk[$login]['first_name'],
                'groupOld'   => '',
                'groupNew'   => $targetGroup,
                'email'      => $lk[$login]['email'],
            ]);
        }

        // ── Отчисление: есть в Moodle, нет в ЛК ──
        foreach ($onlyMoodle as $login) {
            $this->addChange([
                'type'       => 'expel',
                'reason'     => 'Есть в Мудл, нет в ЛК',
                'login'      => $login,
                'surnameOld' => $moodle[$login]['last_name'],
                'surnameNew' => '',
                'nameOld'    => $moodle[$login]['first_name'],
                'nameNew'    => '',
                'groupOld'   => $moodle[$login]['group'],
                'groupNew'   => '',
                'email'      => $moodle[$login]['email'],
            ]);
        }

        // ── Сравнение ФИО и группы для общих студентов ──
        foreach ($both as $login) {
            $lkRow = $lk[$login];
            $moodleRow = $moodle[$login];
            $targetGroup = $this->targetGroup($lkRow['group']);

            $surnameDiff = $this->norm($lkRow['last_name']) !== $this->norm($moodleRow['last_name']);
            $nameDiff = $this->norm($lkRow['first_name']) !== $this->norm($moodleRow['first_name']);
            $groupDiff = $this->norm($targetGroup) !== $this->norm($moodleRow['group']);

            // ── Различие ФИО ──
            if ($surnameDiff || $nameDiff) {
                $this->addChange([
                    'type'       => 'fio',
                    'reason'     => 'Различие ФИО',
                    'login'      => $login,
                    'surnameOld' => $moodleRow['last_name'],
                    'surnameNew' => $lkRow['last_name'],
                    'nameOld'    => $moodleRow['first_name'],
                    'nameNew'    => $lkRow['first_name'],
                    'groupOld'   => $moodleRow['group'],
                    'groupNew'   => $moodleRow['group'],
                    'email'      => '',
                ]);
            }

            // ── Различие группы (перевод) ──
            if ($groupDiff) {
                $this->addChange([
                    'type'       => 'transfer',
                    'reason'     => 'Различие группы',
                    'login'      => $login,
                    'surnameOld' => $moodleRow['last_name'],
                    'surnameNew' => $moodleRow['last_name'],
                    'nameOld'    => $moodleRow['first_name'],
                    'nameNew'    => $moodleRow['first_name'],
                    'groupOld'   => $moodleRow['group'],
                    'groupNew'   => $targetGroup,
                    'email'      => '',
                ]);
            }
        }
    }

    // ─── Вспомогательные методы сравнения ──────────────────────

    /**
     * Добавление изменения в список $this->changes.
     *
     * @param  array{type: string, reason: string, login: string, ...}  $data  Данные изменения
     */
    private function addChange(array $data): void
    {
        $id = count($this->changes);
        $data['id'] = $id;
        $this->changes[] = $data;
        $this->selected[$id] = true;
        $this->stats[$data['type']]++;
    }

    /**
     * Нормализация строки для сравнения: trim + mb_strtolower.
     *
     * @param  string  $value
     * @return string
     */
    private function norm(string $value): string
    {
        return mb_strtolower(trim($value));
    }

    /**
     * Определение целевой группы студента.
     *
     * Заглушка: возвращает группу как есть.
     * В будущем здесь может быть логика определения группы на следующий курс.
     *
     * @param  string  $group  Текущая группа
     * @return string  Целевая группа
     */
    private function targetGroup(string $group): string
    {
        return $group;
    }

    /**
     * Генерация хеша пароля в формате SHA-512 crypt ($6$rounds=10000$...).
     *
     * @param  string  $password  Открытый текст пароля
     * @return string  Хеш для mdl_user.password
     */
    private function hashPassword(string $password): string
    {
        $salt = bin2hex(random_bytes(16));
        return crypt($password, '$6$rounds=10000$' . $salt . '$');
    }

    // ─── Генерация SQL ─────────────────────────────────────────

    /**
     * Генерация SQL-запроса для Moodle на основе выбранных изменений.
     *
     * Структура SQL:
     *   - START TRANSACTION
     *   - Карта группа → ID когорты (плейсхолдеры)
     *   - Блок 1: INSERT INTO mdl_user (зачисление)
     *   - Блок 2: UPDATE mdl_user SET lastname/firstname (смена ФИО)
     *   - Блок 3: DELETE FROM mdl_cohort_members (удаление из старых когорт)
     *   - Блок 4: INSERT INTO mdl_cohort_members (добавление в новые когорты)
     *   - Блок 5: DELETE + UPDATE SET deleted=1 (отчисление)
     *   - COMMIT
     *
     * Плейсхолдеры когорт заменить на реальные ID (mdl_cohort.id) перед выполнением.
     *
     * @return string  Готовый SQL-запрос
     */
    private function buildSql(): string
    {
        $selectedIds = array_keys(array_filter($this->selected, fn ($v) => $v));
        $rows = collect($this->changes)->whereIn('id', $selectedIds);

        $enroll = $rows->where('type', 'enroll');
        $fio = $rows->where('type', 'fio');
        $transfer = $rows->where('type', 'transfer');
        $expel = $rows->where('type', 'expel');

        // ── Сбор использованных групп ──
        $groupsUsed = [];
        foreach ($rows as $r) {
            if (in_array($r['type'], ['enroll', 'transfer']) && $r['groupNew'] !== '') {
                $groupsUsed[$r['groupNew']] = true;
            }
            if (in_array($r['type'], ['transfer', 'expel']) && $r['groupOld'] !== '') {
                $groupsUsed[$r['groupOld']] = true;
            }
        }

        // ── Карта группа → плейсхолдер когорты ──
        $cohortMap = [];
        $i = 0;
        foreach (array_keys($groupsUsed) as $group) {
            $i++;
            $cohortMap[$group] = '0 /* COHORT_ID_' . $i . ' */';
        }

        // ── Шапка SQL ──
        $sql = "START TRANSACTION;\n\n";

        $sql .= "-- ============================================================\n";
        $sql .= "-- КАРТА ГРУППА -> ID КОГОРТЫ (mdl_cohort.id в Moodle)\n";
        $sql .= "-- Замените плейсхолдеры на реальные cohortid перед выполнением:\n";
        foreach ($cohortMap as $group => $token) {
            $sql .= "--   $token  (группа: \"" . $this->commentSafe($group) . "\")\n";
        }
        $sql .= "-- ============================================================\n\n";

        // ── Блок 1: Создание пользователей ──
        if ($enroll->isNotEmpty()) {
            $sql .= "-- ============================================================\n";
            $sql .= "-- БЛОК 1: СОЗДАНИЕ НОВЫХ ПОЛЬЗОВАТЕЛЕЙ (только в ЛК)\n";
            $sql .= "-- Пароль по умолчанию: Iubip_1234567\n";
            $sql .= "-- ============================================================\n";
            $sql .= "INSERT INTO mdl_user (username, password, firstname, lastname, email, auth, confirmed, timecreated) VALUES\n";
            $vals = [];
            foreach ($enroll as $r) {
                $hash = $this->hashPassword('Iubip_1234567');
                $vals[] = sprintf(
                    "('%s', '%s', '%s', '%s', '%s', 'manual', 1, UNIX_TIMESTAMP())",
                    $this->esc($r['login']),
                    $this->esc($hash),
                    $this->esc($r['nameNew']),
                    $this->esc($r['surnameNew']),
                    $this->esc($r['email'])
                );
            }
            $sql .= implode(",\n", $vals) . ";\n\n";
        }

        // ── Блок 2: Обновление ФИО ──
        if ($fio->isNotEmpty()) {
            $sql .= "-- ============================================================\n";
            $sql .= "-- БЛОК 2: ОБНОВЛЕНИЕ ФАМИЛИЙ И ИМЁН\n";
            $sql .= "-- ============================================================\n";
            $sql .= "UPDATE mdl_user SET\n";
            $sql .= "    lastname = CASE username\n";
            foreach ($fio as $r) {
                $sql .= "        WHEN '" . $this->esc($r['login']) . "' THEN '" . $this->esc($r['surnameNew']) . "'\n";
            }
            $sql .= "    END,\n";
            $sql .= "    firstname = CASE username\n";
            foreach ($fio as $r) {
                $sql .= "        WHEN '" . $this->esc($r['login']) . "' THEN '" . $this->esc($r['nameNew']) . "'\n";
            }
            $sql .= "    END\n";
            $sql .= "WHERE username IN (" . $this->inList($fio->pluck('login')) . ");\n\n";
        }

        // ── Блок 3: Удаление из старых когорт ──
        if ($transfer->isNotEmpty()) {
            $byOldGroup = $transfer->groupBy('groupOld');
            $sql .= "-- ============================================================\n";
            $sql .= "-- БЛОК 3: УДАЛЕНИЕ ИЗ СТАРЫХ ГРУПП\n";
            $sql .= "-- ============================================================\n";
            foreach ($byOldGroup as $group => $items) {
                $token = $cohortMap[$group] ?? 'NULL';
                $sql .= "-- Старая группа: \"" . $this->commentSafe($group) . "\"\n";
                $sql .= "DELETE FROM mdl_cohort_members\n";
                $sql .= "WHERE cohortid = $token\n";
                $sql .= "  AND userid IN (SELECT id FROM mdl_user WHERE username IN (" . $this->inList($items->pluck('login')) . "));\n\n";
            }
        }

        // ── Блок 4: Добавление в новые когорты ──
        $addToGroup = $enroll->merge($transfer);
        if ($addToGroup->isNotEmpty()) {
            $byNewGroup = $addToGroup->groupBy('groupNew');
            $sql .= "-- ============================================================\n";
            $sql .= "-- БЛОК 4: ДОБАВЛЕНИЕ В НОВЫЕ ГРУППЫ\n";
            $sql .= "-- ============================================================\n";
            foreach ($byNewGroup as $group => $items) {
                if ($group === '') {
                    continue;
                }
                $token = $cohortMap[$group] ?? 'NULL';
                $sql .= "-- Новая группа: \"" . $this->commentSafe($group) . "\"\n";
                $sql .= "INSERT INTO mdl_cohort_members (cohortid, userid, timeadded)\n";
                $sql .= "SELECT $token, id, UNIX_TIMESTAMP()\n";
                $sql .= "FROM mdl_user WHERE username IN (" . $this->inList($items->pluck('login')) . ");\n\n";
            }
        }

        // ── Блок 5: Отчисление ──
        if ($expel->isNotEmpty()) {
            $byOldGroup = $expel->groupBy('groupOld');
            $sql .= "-- ============================================================\n";
            $sql .= "-- БЛОК 5: ОТЧИСЛЕНИЕ (если студент только в Мудл)\n";
            $sql .= "-- ============================================================\n";
            foreach ($byOldGroup as $group => $items) {
                $token = $cohortMap[$group] ?? 'NULL';
                $logins = $this->inList($items->pluck('login'));
                $sql .= "-- Группа: \"" . $this->commentSafe($group) . "\"\n";
                $sql .= "DELETE FROM mdl_cohort_members\n";
                $sql .= "WHERE cohortid = $token\n";
                $sql .= "  AND userid IN (SELECT id FROM mdl_user WHERE username IN ($logins));\n";
                $sql .= "UPDATE mdl_user SET deleted = 1\n";
                $sql .= "WHERE username IN ($logins);\n\n";
            }
        }

        $sql .= "COMMIT;\n";

        return $sql;
    }

    // ─── SQL-утилиты ───────────────────────────────────────────

    /**
     * Формирование SQL IN-списка логинов с экранированием.
     *
     * @param  \Illuminate\Support\Collection  $logins  Коллекция логинов
     * @return string  Напр. "'st100001', 'st100002'"
     */
    private function inList($logins): string
    {
        return $logins->map(fn ($l) => "'" . $this->esc($l) . "'")->implode(', ');
    }

    /**
     * Экранирование значения для безопасной вставки в SQL-строку (MySQL).
     *
     * Обрабатывает: обратный слэш, NUL, \n, \r, Ctrl-Z, одинарную кавычку.
     * Не экранирует двойные кавычки и бэктики (не нужны внутри одинарных кавычек MySQL).
     *
     * @param  string  $value  Исходное значение
     * @return string  Экранированное значение
     */
    private function esc(string $value): string
    {
        return str_replace(
            ["\\", "\0", "\n", "\r", "\x1a", "'"],
            ["\\\\", "\\0", "\\n", "\\r", "\\Z", "''"],
            $value
        );
    }

    /**
     * Очистка значения для безопасной вставки в SQL-комментарий.
     *
     * Удаляет control-символы (\x00–\x1f), \r, \n.
     * Сохраняет дефисы (нужны в названиях групп: ИТ-24 К1).
     *
     * @param  string  $value  Исходное значение
     * @return string  Очищенное значение
     */
    private function commentSafe(string $value): string
    {
        return preg_replace('/[\x00-\x1f\r\n]/', ' ', $value);
    }

    // ─── SQL-запрос Moodle ─────────────────────────────────────

    /**
     * Возвращает SQL-запрос для выборки всех студентов из видимых когорт Moodle.
     *
     * Используется в Step 1: пользователь копирует запрос, выполняет в Moodle,
     * выгружает результат в CSV для загрузки в Step 2.
     *
     * Фильтры:
     *   - видимые когорты (c.visible = 1)
     *   - названия не начинаются с цифры
     *   - не аспирантура
     *   - латинские символы исключены
     *   - только студенты (role = student)
     *   - soft-deleted записи исключены
     *
     * @return string  SQL-запрос
     */
    private function getMoodleQuery(): string
    {
        return <<<'SQL'
-- ============================================================
-- ВСЕ СТУДЕНТЫ В ВИДИМЫХ ГРУППАХ (КОГОРТАХ)
-- ФАМИЛИЯ И ИМЯ РАЗДЕЛЬНО
-- ============================================================
SELECT
    u.username AS st_ticket,
    u.firstname AS last_name,
    u.lastname  AS first_name,
    u.email AS email,
    c.name AS group_name_fixed
FROM `mdl_user` u
JOIN `mdl_cohort_members` cm ON cm.userid = u.id
JOIN `mdl_cohort` c ON c.id = cm.cohortid
WHERE
    c.visible = 1
    AND c.name NOT REGEXP '^[0-9]'
    AND c.name NOT LIKE 'Асп%'
    AND c.name NOT REGEXP '[A-Za-z]'
    AND u.deleted = 0
    AND EXISTS (
        SELECT 1
        FROM `mdl_role_assignments` ra
        JOIN `mdl_role` r ON r.id = ra.roleid
        WHERE ra.userid = u.id
          AND r.shortname = 'student'
    )
    AND EXISTS (
        SELECT 1
        FROM `mdl_cohort_members` cm2
        WHERE cm2.cohortid = c.id
    )
GROUP BY
    u.id, u.username, u.lastname, u.firstname, u.email, c.name
ORDER BY
    c.name, u.lastname, u.firstname;
SQL;
    }
}
