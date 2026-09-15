<?php

namespace App\Livewire\Employee;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Управление контингентом: сравнение данных ЛК и Moodle.
 *
 * Пайплайн:
 *   1. Получить список студентов из БД ЛК (кеш 30 мин).
 *   2. Загрузить CSV-файл с данными Moodle (стриминг, построчно).
 *   3. Сравнить по логину → определить зачисления, отчисления, смены ФИО, переводы.
 *   4. Сгенерировать SQL-запрос (Блоки 1–5) для приведения Moodle в соответствие с ЛК.
 *
 * Особенности ФИО:
 *   - В ЛК фамилия/имя/отчество хранятся раздельно (users_data).
 *   - В Moodle всего два поля (firstname/lastname), отчество НЕ закреплено:
 *     может лежать в любом из них, сами поля тоже могут быть переставлены
 *     (при создании их меняют местами, т.к. Moodle сортирует списки по firstname).
 *   - Поэтому сравнение ФИО — по неупорядоченному набору слов полного ФИО
 *     (регистр/ё/пробелы/раскладка по полям игнорируются).
 *   - Обновление ФИО (Блок 2) сохраняет существующую раскладку пользователя:
 *     фамилия пишется в ту же колонку, где уже лежит, «Имя Отчество» — в другую.
 *     Новых пользователей (Блок 1) создаём в перевёрнутой раскладке
 *     (firstname=Фамилия, lastname=Имя Отчество), как при ручном создании.
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

    /**
     * Флаг: данные ЛК из БД недоступны (запрос упал).
     *
     * При недоступном ЛК нельзя строить SQL: сравнение с пустым списком ЛК
     * пометило бы всех студентов Мудл как «отчисление», а генерация такого
     * скрипта массово удалила бы контингент. Пока флаг true, buildPreview/
     * downloadSql заблокированы.
     */
    public bool $lkUnavailable = false;

    /**
     * Список найденных расхождений (массив ассоциативных массивов).
     *
     * Защищённое свойство: НЕ сериализуется в снапшоте Livewire.
     * Большой массив (тысячи строк) гонялся бы в браузере при каждом
     * обновлении (чекбокс, пагинация, вкладка) и каждый переход по
     * страницам таблицы стоил бы мегабайты трафика. Хранится на сервере
     * в кеше, а в Livewire уходит только ключ ($changesKey).
     */
    protected array $changes = [];

    /** Ключ кеша с массивом $changes (загружается в boot() на каждый запрос). */
    public string $changesKey = '';

    /** Активная вкладка таблиц результатов ('fio'|'enroll'|'transfer'|'expel'). */
    public string $activeTab = 'fio';

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

    /**
     * Построенный SQL-запрос для Moodle (см. computed sql()).
     *
     * Сам текст НЕ сериализуется в снапшоте Livewire — на тысячах изменений
     * это мегабайты, которые гонялись бы в браузере при каждом чихе.
     * Хранится в кеше по ключу ($sqlKey), а клиент получает его точечно
     * через событие copy-sql (кнопка «Копировать запрос»).
     */
    public string $sqlKey = '';

    /** Флаг: предпросмотр SQL актуален (сбрасывается при смене чекбоксов). */
    public bool $sqlBuilt = false;

    /** SQL-запрос для выборки студентов из Moodle (скопировать в Step 1). */
    public string $moodleQuery = '';

    /** Предупреждения при парсинге CSV (дубли, пропущенные строки). */
    public array $csvWarnings = [];

    /**
     * Предупреждения по данным ЛК. Сейчас: студенты в нескольких активных
     * группах — для них берётся одна (последняя по сортировке) группа, что
     * может не совпасть с реальным переводом в Moodle.
     */
    public array $lkWarnings = [];

    /** Текущие страницы таблиц результатов (пагинация — против зависания на тысячах строк). */
    public array $page = ['fio' => 1, 'enroll' => 1, 'transfer' => 1, 'expel' => 1];

    /** Строк на страницу в таблицах результатов. */
    public int $perPage = 50;

    // ─── Жизненный цикл Livewire ────────────────────────────────

    /**
     * На каждый запрос Livewire достаёт массив изменений из кеша.
     *
     * Сам массив не сериализуется в снапшот (см. свойство $changes),
     * поэтому он не гоняется в браузер, а после гидрации компонент
     * восстанавливает его на сервере по ключу $changesKey.
     */
    public function boot(): void
    {
        if ($this->changesKey !== '') {
            $this->changes = Cache::get($this->changesKey, []);
        }
    }

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
     *
     * ВАЖНО: SQL здесь НЕ строится — на тысячах изменений это мегабайты текста,
     * которые Livewire гонял бы в браузере при каждом чихе (чекбокс, пагинация)
     * и страница висла. SQL строится только по кнопке (buildPreview/downloadSql/copySql).
     */
    public function render()
    {
        return view('livewire.employee.moodle-contingent-sync');
    }

    /**
     * Построенный SQL-запрос для Moodle.
     *
     * Computed-свойство: НЕ сериализуется в снапшоте Livewire (в шаблоне
     * доступно как $this->sql). Читает текст из кеша по ключу $sqlKey.
     *
     * @return string  SQL (пустая строка, если не построен или устарел)
     */
    #[Computed]
    public function sql(): string
    {
        if ($this->sqlKey === '') {
            return '';
        }

        return Cache::get($this->sqlKey, '');
    }

    /**
     * Инвалидация построенного SQL: стирает кеш и сбрасывает флаг актуальности.
     */
    private function invalidateSql(): void
    {
        if ($this->sqlKey !== '') {
            Cache::forget($this->sqlKey);
            $this->sqlKey = '';
        }
        $this->sqlBuilt = false;
    }

    /**
     * Сохранение построенного SQL в кеш (текст не уходит в снапшот Livewire).
     */
    private function storeSql(string $sql): void
    {
        $this->sqlKey = 'moodle:sql:' . Str::random(20);
        Cache::put($this->sqlKey, $sql, now()->addHours(6));
        $this->sqlBuilt = true;
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

        // Сначала убеждаемся, что файл есть и корректно парсится.
        // Только после успешного парсинга стираем предыдущий результат —
        // иначе ошибка «загрузите файл» уничтожила бы готовую таблицу.
        if (! $this->csvFile) {
            $this->addError('csvFile', 'Загрузите CSV-файл с данными Мудл.');
            return;
        }

        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:20480',
        ]);

        try {
            $moodle = $this->parseMoodleCsv($this->csvFile->getRealPath());
        } catch (Exception $e) {
            $this->addError('csvFile', 'Не удалось разобрать CSV Мудл: ' . $e->getMessage());
            return;
        }

        $this->reset('selected', 'stats', 'parsed', 'lkUnavailable', 'lkWarnings');
        $this->stats = ['enroll' => 0, 'expel' => 0, 'fio' => 0, 'transfer' => 0];
        $this->page = ['fio' => 1, 'enroll' => 1, 'transfer' => 1, 'expel' => 1];
        $this->invalidateSql();
        $this->activeTab = 'fio';

        // Старые данные из кеша больше не нужны — очищаем, чтобы не копить ключи.
        if ($this->changesKey !== '') {
            Cache::forget($this->changesKey);
            $this->changesKey = '';
        }
        $this->changes = [];

        try {
            $lk = $this->getLkStudents();
        } catch (Exception $e) {
            Cache::forget('lk:students');
            $lk = [];
            $this->lkUnavailable = true;
            $this->dispatch(
                'show-toast',
                message: 'Данные ЛК из БД недоступны — генерация SQL заблокирована (иначе весь контингент Мудл был бы помечен как «отчисление»).',
                type: 'warning'
            );
        }

        $this->compare($lk, $moodle);

        $this->parsed = true;
        // Складываем массив изменений в кеш на сервере — в снапшот Livewire
        // уходит только ключ, и пагинация/чекбоксы не тянут мегабайты данных.
        $this->changesKey = 'moodle:sync:' . Str::random(20);
        Cache::put($this->changesKey, $this->changes, now()->addHours(6));
        $this->dispatch('show-toast', message: 'Сравнение выполнено. Найдено изменений: ' . count($this->changes), type: 'success');
    }

    /**
     * Копирование SQL в буфер обмена (кнопка «Копировать запрос»).
     *
     * SQL-текст передаётся один раз — событием copy-sql в JS после нажатия,
     * а не висит в DOM (bag: раньше весь SQL лежал в скрытом div
     * #sql-clipboard-source и утяжелял страницу при больших синхронизациях).
     *
     * SQL строится прямо здесь, если ещё не построен, — кнопка работает
     * сразу, без предварительного «Показать SQL».
     */
    public function copySql(): void
    {
        if ($this->lkUnavailable) {
            $this->dispatch('show-toast', message: 'Данные ЛК недоступны — генерация SQL заблокирована.', type: 'error');
            return;
        }

        if (empty($this->changes)) {
            $this->dispatch('show-toast', message: 'Сначала загрузите файл — данных для запроса нет.', type: 'error');
            return;
        }

        // Переиспользуем построенный SQL (sqlBuilt сбрасывается при смене выбора),
        // иначе строим по текущим чекбоксам.
        $sql = $this->sqlBuilt ? $this->sql : $this->buildSql();
        $this->storeSql($sql);

        $this->dispatch('copy-sql', sql: $sql);
    }

    /**
     * Переключение активной вкладки таблиц результатов.
     *
     * Вкладки — серверное состояние (не JS): иначе после каждого обновления
     * Livewire, например перехода по странице, DOM возвращался бы к первой
     * вкладке, где в шаблоне нарисована активность по умолчанию.
     */
    public function setTab(string $tab): void
    {
        if (in_array($tab, ['fio', 'enroll', 'transfer', 'expel'], true)) {
            $this->activeTab = $tab;
        }
    }

    /**
     * Переход на страницу таблицы результатов (пагинация против зависания).
     */
    public function gotoPage(string $type, int $page): void
    {
        if (isset($this->page[$type])) {
            $this->page[$type] = max(1, $page);
        }
    }

    /**
     * Массовый выбор/снятие всех изменений одного типа.
     */
    public function toggleType(string $type, bool $value): void
    {
        foreach ($this->changes as $c) {
            if ($c['type'] === $type) {
                $this->selected[$c['id']] = $value;
            }
        }
        $this->invalidateSql();
    }

    /**
     * Любое изменение чекбоксов делает предпросмотр SQL неактуальным.
     */
    public function updatedSelected(): void
    {
        $this->invalidateSql();
    }

    /**
     * Построение предпросмотра SQL по требованию (кнопка, а не каждый рендер).
     */
    public function buildPreview(): void
    {
        if ($this->lkUnavailable) {
            $this->dispatch('show-toast', message: 'Данные ЛК недоступны — генерация SQL заблокирована.', type: 'error');
            return;
        }

        if (empty($this->changes)) {
            $this->dispatch('show-toast', message: 'Нет данных для предпросмотра. Сначала загрузите файл.', type: 'error');
            return;
        }

        $this->storeSql($this->buildSql());
    }

    /**
     * Изменения одного типа с пагинацией (для таблиц результатов).
     *
     * @return array{items: array, total: int, pages: int, page: int}
     */
    private function pagedChanges(string $type): array
    {
        $filtered = array_values(array_filter($this->changes, fn ($c) => $c['type'] === $type));
        $total = count($filtered);
        $pages = max(1, (int) ceil($total / $this->perPage));
        $page = min(max(1, $this->page[$type] ?? 1), $pages);
        $this->page[$type] = $page;

        return [
            'items' => array_slice($filtered, ($page - 1) * $this->perPage, $this->perPage),
            'total' => $total,
            'pages' => $pages,
            'page'  => $page,
        ];
    }

    /** @return array{items: array, total: int, pages: int, page: int} */
    public function getFioChangesProperty(): array
    {
        return $this->pagedChanges('fio');
    }

    /** @return array{items: array, total: int, pages: int, page: int} */
    public function getEnrollChangesProperty(): array
    {
        return $this->pagedChanges('enroll');
    }

    /** @return array{items: array, total: int, pages: int, page: int} */
    public function getTransferChangesProperty(): array
    {
        return $this->pagedChanges('transfer');
    }

    /** @return array{items: array, total: int, pages: int, page: int} */
    public function getExpelChangesProperty(): array
    {
        return $this->pagedChanges('expel');
    }

    /**
     * Скачивание сгенерированного SQL-запроса в .txt-файл.
     */
    public function downloadSql()
    {
        if ($this->lkUnavailable) {
            $this->dispatch('show-toast', message: 'Данные ЛК недоступны — генерация SQL заблокирована.', type: 'error');
            return;
        }

        if (empty($this->changes)) {
            $this->dispatch('show-toast', message: 'Нет данных для выгрузки. Сначала загрузите файл.', type: 'error');
            return;
        }

        // Переиспользуем уже построенный SQL, если выбор чекбоксов не менялся
        // (sqlBuilt сбрасывается при любом изменении выбора), иначе строим заново.
        $sql = $this->sqlBuilt ? $this->sql : $this->buildSql();
        $this->storeSql($sql);

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
    /**
     * Маппинг имён колонок CSV Moodle в ключи данных.
     *
     * @param  array<int, string>  $header  Нормализованные имена колонок (нижний регистр, trim)
     * @return array<string, int>  ['login' => idx, 'last_name' => idx, ...]
     */
    private function mapMoodleCsvHeader(array $header): array
    {
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

        return $map;
    }

    private function parseMoodleCsv(string $path): array
    {
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            throw new Exception('Файл недоступен.');
        }

        try {
            // ── Образец для определения кодировки: заголовок + до 100 строк ──
            // (одного заголовка мало: английские имена колонок — чистый ASCII,
            // а кириллица в windows-1251 появится только в строках данных).
            $sampleLines = [];
            while (count($sampleLines) < 101 && ($line = fgets($handle)) !== false) {
                if (trim($line) !== '') {
                    $sampleLines[] = $line;
                }
            }
            if (empty($sampleLines)) {
                throw new Exception('Файл пуст.');
            }

            // ── Определение кодировки ──
            //
            // Решение — по большинству строк образца: если >=90% строк
            // валидны как UTF-8, файл считаем UTF-8, а битые байты
            // в отдельных строках заменяем на «?» построчно (портится
            // только эта строка, а не весь файл). Иначе — детект
            // (обычно windows-1251) и конвертация. Это защищает от
            // ситуации, когда пара битых байт в огромном файле сбивает
            // детект и весь файл превращается в кракозябры.
            $validUtf8 = 0;
            foreach ($sampleLines as $sampleLine) {
                if (mb_check_encoding($sampleLine, 'UTF-8')) {
                    $validUtf8++;
                }
            }
            if ($validUtf8 >= count($sampleLines) * 0.9) {
                $convert = fn (string $s): string => mb_convert_encoding($s, 'UTF-8', 'UTF-8');
            } else {
                $encoding = mb_detect_encoding(
                    implode('', $sampleLines),
                    ['windows-1251', 'ISO-8859-1', 'ASCII', 'UTF-8'],
                    true
                );
                $convert = ($encoding !== false && $encoding !== 'UTF-8')
                    ? fn (string $s): string => mb_convert_encoding($s, 'UTF-8', $encoding)
                    : fn (string $s): string => $s;
            }

            // ── Определение разделителя и заголовков ──
            $firstLine = $convert($sampleLines[0]);
            // Многие экспорты (Moodle/Excel «CSV UTF-8») начинаются с BOM —
            // иначе первая колонка «st_ticket» не распознаётся заголовком.
            $firstLine = str_replace("\xEF\xBB\xBF", '', $firstLine);
            $delimiter = str_contains($firstLine, ';') ? ';' : ',';
            $header = array_map(fn ($h) => mb_strtolower(trim($h)), str_getcsv($firstLine, $delimiter, '"', '"'));

            // ── Маппинг колонок ──
            $map = $this->mapMoodleCsvHeader($header);

            // ── Валидация обязательных колонок ──
            foreach (['login', 'last_name', 'first_name', 'group'] as $required) {
                if (! isset($map[$required])) {
                    throw new Exception("В заголовке CSV не найдена колонка '$required'.");
                }
            }
            $maxIdx = max($map['login'], $map['last_name'], $map['first_name'], $map['group']);

            // ── Парсинг строк: сначала образец, затем остаток файла стримингом ──
            $result = [];
            $skippedRows = 0;
            $duplicateLogins = [];
            $seenLogins = [];

            $parseLine = function (string $rawLine) use (
                $convert, $delimiter, $map, $maxIdx,
                &$result, &$skippedRows, &$duplicateLogins, &$seenLogins
            ): void {
                if (trim($rawLine) === '') {
                    return;
                }
                $row = str_getcsv($convert($rawLine), $delimiter, '"', '"');
                if (count($row) <= $maxIdx) {
                    $skippedRows++;
                    return;
                }

                $login = trim($row[$map['login']]);
                if ($login === '') {
                    $skippedRows++;
                    return;
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
                    'group'      => $this->normalizeGroup(trim($row[$map['group']])),
                ];
            };

            // Строки данных из образца (пропускаем заголовок — первую строку).
            for ($i = 1; $i < count($sampleLines); $i++) {
                $parseLine($sampleLines[$i]);
            }
            unset($sampleLines);

            // Остаток файла — построчно, в памяти только одна строка.
            while (($line = fgets($handle)) !== false) {
                $parseLine($line);
            }
        } finally {
            fclose($handle);
        }

        // ── Формирование предупреждений ──
        $this->csvWarnings = [];
        if ($skippedRows > 0) {
            $this->csvWarnings[] = "Пропущено строк: $skippedRows (пустой логин или некорректный формат).";
        }
        if (! empty($duplicateLogins)) {
            // На больших файлах дублей могут быть сотни — показываем первые 20.
            $shown = 0;
            foreach ($duplicateLogins as $dupLogin => $count) {
                if ($shown >= 20) {
                    $rest = count($duplicateLogins) - $shown;
                    $this->csvWarnings[] = "…и ещё дублей логинов: $rest.";
                    break;
                }
                $this->csvWarnings[] = "Дубль логина \"$dupLogin\": $count записей, используется последняя.";
                $shown++;
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
                    sub.middle_name AS middle_name,
                    CONCAT_WS(' ', sub.first_name, sub.middle_name) AS full_name,
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
            ";

            $rows = DB::select($sql);

            $result = [];
            $groupCountByLogin = [];
            foreach ($rows as $row) {
                $login = $row->st_ticket;
                $groupCountByLogin[$login] = ($groupCountByLogin[$login] ?? 0) + 1;
                $result[$login] = [
                    'login'       => $login,
                    'last_name'   => $row->last_name,
                    'first_name'  => $row->first_name,
                    'middle_name' => $row->middle_name ?? '',
                    'email'       => $row->email,
                    'group'       => $row->group_name_fixed,
                ];
            }

            // Студент в нескольких активных группах ЛК: $result[login] сохранила
            // последнюю группу из порядка сортировки — «случайную» с точки зрения
            // оператора. Показываем предупреждение, чтобы перевод не был сюрпризом.
            $this->lkWarnings = $this->multiGroupWarnings($groupCountByLogin);

            return $result;
        });
    }

    /**
     * Человекочитаемые предупреждения о студентах в нескольких группах ЛК.
     *
     * @param  array<string, int>  $groupCountByLogin  логин => кол-во групп
     * @return array<string>  список строк-предупреждений (пусто, если всё ок)
     */
    private function multiGroupWarnings(array $groupCountByLogin): array
    {
        $multiGroup = array_filter($groupCountByLogin, fn ($count) => $count > 1);
        if (! $multiGroup) {
            return [];
        }

        $warnings = ['Студенты в нескольких активных группах ЛК — для них будет использована одна группа (последняя по порядку):'];
        $shown = 0;
        foreach ($multiGroup as $login => $count) {
            if ($shown >= 20) {
                $warnings[] = '…и ещё ' . (count($multiGroup) - $shown) . '.';
                break;
            }
            $warnings[] = "  $login — $count группы";
            $shown++;
        }

        return $warnings;
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
     * Сравнение ФИО — по неупорядоченному набору слов полного имени
     * (см. normalizeFioWords): ЛК «Фамилия Имя Отчество» против обоих
     * полей Moodle скопом. Регистр, ё/е, пробелы, дефисы и раскладка
     * слов по полям Moodle игнорируются. Группы — регистронезависимо.
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
                // «Имя Отчество» целиком — иначе отчество потеряется при создании в Moodle.
                'nameNew'    => $this->lkNameWithPatronymic($lk[$login]),
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
        //
        // ФИО сравнивается как неупорядоченный набор слов полного имени:
        // ЛК «Фамилия Имя Отчество» против обоих полей Moodle скопом.
        // Так переставленные поля Moodle и отчество в любом из них не дают
        // ложных расхождений, а пропущенное отчество — наоборот, находится.
        foreach ($both as $login) {
            $lkRow = $lk[$login];
            $moodleRow = $moodle[$login];
            $targetGroup = $this->targetGroup($lkRow['group']);

            $fioDiff = $this->normalizeFioWords($this->lkFullName($lkRow))
                !== $this->normalizeFioWords($this->moodleFullName($moodleRow));
            $groupDiff = $this->norm($targetGroup) !== $this->norm($moodleRow['group']);

            // ── Различие ФИО ──
            if ($fioDiff) {
                $this->addChange([
                    'type'       => 'fio',
                    'reason'     => 'Различие ФИО',
                    'login'      => $login,
                    'surnameOld' => $moodleRow['last_name'],
                    'surnameNew' => $lkRow['last_name'],
                    'nameOld'    => $moodleRow['first_name'],
                    // «Имя Отчество» целиком — иначе отчество затрётся в Moodle.
                    'nameNew'    => $this->lkNameWithPatronymic($lkRow),
                    'groupOld'   => $moodleRow['group'],
                    'groupNew'   => $moodleRow['group'],
                    'email'      => '',
                    // Текущая раскладка пользователя в Moodle (для Блока 2 SQL):
                    // true — фамилия в колонке Moodle `firstname`.
                    'surnameInFirstname' => $this->isSurnameInMoodleFirstname($lkRow, $moodleRow),
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
     * Нормализация ФИО в неупорядоченный набор слов для сравнения.
     *
     * Игнорируются: регистр, буква ё/е, лишние пробелы, дефисы/тире
     * (чтобы «Салтыков-Щедрин» и «Салтыков Щедрин» совпадали),
     * а также раскладка слов по полям firstname/lastname Moodle.
     *
     * @param  string  $value  Произвольная строка ФИО (одно или оба поля Moodle, полное ФИО ЛК)
     * @return string  Отсортированные слова через пробел, напр. «иван иванов петрович»
     */
    private function normalizeFioWords(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = str_replace('ё', 'е', $value);
        // Дефисы/тире всех видов — в пробелы, чтобы двойные фамилии сравнивались одинаково.
        $value = str_replace(['-', '‐', '‑', '‒', '–', '—', '―'], ' ', $value);
        $words = preg_split('/\s+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        if ($words === false) {
            return '';
        }
        sort($words);
        return implode(' ', $words);
    }

    /**
     * Полное ФИО студента ЛК одной строкой: «Фамилия Имя Отчество» (без двойных пробелов).
     */
    private function lkFullName(array $lkRow): string
    {
        return trim(($lkRow['last_name'] ?? '') . ' ' . ($lkRow['first_name'] ?? '') . ' ' . ($lkRow['middle_name'] ?? ''));
    }

    /**
     * «Имя Отчество» студента ЛК — значение для колонки Moodle с именем.
     */
    private function lkNameWithPatronymic(array $lkRow): string
    {
        return trim(($lkRow['first_name'] ?? '') . ' ' . ($lkRow['middle_name'] ?? ''));
    }

    /**
     * Полное ФИО студента Moodle одной строкой (оба поля, порядок не важен).
     *
     * ВАЖНО: из-за маппинга запроса Moodle ($this->getMoodleQuery())
     * $moodleRow['last_name'] — это колонка Moodle `firstname`,
     * $moodleRow['first_name'] — это колонка Moodle `lastname`.
     */
    private function moodleFullName(array $moodleRow): string
    {
        return trim(($moodleRow['last_name'] ?? '') . ' ' . ($moodleRow['first_name'] ?? ''));
    }

    /**
     * Определение текущей раскладки пользователя в Moodle.
     *
     * Возвращает true, если фамилия лежит в колонке Moodle `firstname`
     * (в наших данных это $moodleRow['last_name'] из-за маппинга запроса),
     * и false, если фамилия лежит в колонке Moodle `lastname`.
     *
     * Сравниваем по вхождению слов фамилии ЛК в слова каждого поля:
     * побеждает поле с большим числом совпадений. При ничьей —
     * перевёрнутая раскладка (фамилия в firstname), как при ручном создании.
     */
    private function isSurnameInMoodleFirstname(array $lkRow, array $moodleRow): bool
    {
        $surnameWords = explode(' ', $this->normalizeFioWords($lkRow['last_name'] ?? ''));
        $surnameWords = array_filter($surnameWords);
        if (empty($surnameWords)) {
            return true;
        }

        // $moodleRow['last_name'] = колонка Moodle `firstname` (маппинг запроса).
        $inFirstname = explode(' ', $this->normalizeFioWords($moodleRow['last_name'] ?? ''));
        // $moodleRow['first_name'] = колонка Moodle `lastname` (маппинг запроса).
        $inLastname = explode(' ', $this->normalizeFioWords($moodleRow['first_name'] ?? ''));

        $hitsFirst = count(array_intersect($surnameWords, $inFirstname));
        $hitsLast = count(array_intersect($surnameWords, $inLastname));

        return $hitsFirst >= $hitsLast;
    }

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
     * Нормализация названия группы по шаблону «генерированный_группа»
     * (см. запрос getLkStudents): убираются пробелы и текущий год из
     * названия, затем добавляется ` (текущий год)`.
     *
     * Повторяет в PHP выражение из SQL-шаблона, чтобы группы из CSV Moodle
     * приводились к тому же формату, что и группы из ЛК:
     *   "МГ-24 Н1"        -> "МГ-24Н1 (2026)"
     *   "МГ-24Н1 (2026)"  -> "МГ-24Н1 (2026)"
     *
     * @param  string  $group  Название группы из CSV/БД
     * @return string  Нормализованное название группы
     */
    private function normalizeGroup(string $group): string
    {
        $year = (string) date('Y');

        // Уже нормализованное название («БАЗА (год)») не трогаем — иначе
        // повторное применение выражения из шаблона даст «БАЗА() (год)».
        if (preg_match('/\s\(\d{4}\)$/u', $group)) {
            return $group;
        }

        $clean = str_replace(' ', '', $group);
        $clean = str_replace($year, '', $clean);
        $clean = str_replace('(' . $year . ')', '', $clean);

        return trim($clean) . ' (' . $year . ')';
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
            // Перевёрнутая раскладка, как при ручном создании:
            // Moodle firstname = Фамилия (для сортировки списков),
            // Moodle lastname = «Имя Отчество».
            $sql .= "INSERT INTO mdl_user (username, password, firstname, lastname, email, auth, confirmed, timecreated) VALUES\n";
            // Один хеш на всех: пароль одинаковый (Iubip_1234567), а crypt() —
            // SHA-512, 10 000 раундов — считает ~0.1 с на пользователя.
            // На тысячах зачислений это десятки секунд, поэтому хеш считаем раз.
            $hash = $this->hashPassword('Iubip_1234567');
            $vals = [];
            foreach ($enroll as $r) {
                $vals[] = sprintf(
                    "('%s', '%s', '%s', '%s', '%s', 'manual', 1, UNIX_TIMESTAMP())",
                    $this->esc($r['login']),
                    $this->esc($hash),
                    $this->esc($r['surnameNew']),
                    $this->esc($r['nameNew']),
                    $this->esc($r['email'])
                );
            }
            $sql .= implode(",\n", $vals) . ";\n\n";
        }

        // ── Блок 2: Обновление ФИО ──
        //
        // Сохраняем существующую раскладку каждого пользователя:
        // фамилия пишется в ту же колонку Moodle, где уже лежит,
        // «Имя Отчество» — в другую. Поэтому групп две (максимум два UPDATE).
        if ($fio->isNotEmpty()) {
            $sql .= "-- ============================================================\n";
            $sql .= "-- БЛОК 2: ОБНОВЛЕНИЕ ФАМИЛИЙ И ИМЁН\n";
            $sql .= "-- Раскладка каждого пользователя сохраняется: фамилия пишется\n";
            $sql .= "-- в ту же колонку, где уже лежит, «Имя Отчество» — в другую.\n";
            $sql .= "-- ============================================================\n";

            [$fioFirst, $fioLast] = $this->splitFioByLayout($fio);

            if ($fioFirst->isNotEmpty()) {
                // Фамилия уже в Moodle `firstname` (перевёрнутая раскладка):
                // firstname = Фамилия, lastname = «Имя Отчество».
                $sql .= "-- Фамилия в колонке firstname (перевёрнутая раскладка)\n";
                $sql .= $this->fioUpdateSql($fioFirst, 'firstname', 'lastname');
            }

            if ($fioLast->isNotEmpty()) {
                // Фамилия уже в Moodle `lastname` (стандартная раскладка):
                // firstname = «Имя Отчество», lastname = Фамилия.
                $sql .= "-- Фамилия в колонке lastname (стандартная раскладка)\n";
                $sql .= $this->fioUpdateSql($fioLast, 'lastname', 'firstname');
            }
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

    /**
     * Разделение ФИО-изменений по текущей раскладке пользователя в Moodle.
     *
     * @param  \Illuminate\Support\Collection  $fio  Изменения типа 'fio'
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection}
     *         [0] — фамилия в Moodle `firstname`, [1] — фамилия в Moodle `lastname`.
     *         Записи без флага (старые/ручные) считаются перевёрнутой раскладкой.
     */
    private function splitFioByLayout($fio): array
    {
        $inFirstname = $fio->filter(fn ($r) => ($r['surnameInFirstname'] ?? true) === true)->values();
        $inLastname = $fio->filter(fn ($r) => ($r['surnameInFirstname'] ?? true) === false)->values();

        return [$inFirstname, $inLastname];
    }

    /**
     * Один UPDATE mdl_user для группы пользователей с одинаковой раскладкой.
     *
     * @param  \Illuminate\Support\Collection  $rows            Изменения типа 'fio'
     * @param  string                          $surnameColumn   Колонка для фамилии ('firstname'|'lastname')
     * @param  string                          $nameColumn      Колонка для «Имя Отчество»
     * @return string  Готовый UPDATE ... CASE ... оператор
     */
    private function fioUpdateSql($rows, string $surnameColumn, string $nameColumn): string
    {
        $sql = "UPDATE mdl_user SET\n";
        $sql .= "    $surnameColumn = CASE username\n";
        foreach ($rows as $r) {
            $sql .= "        WHEN '" . $this->esc($r['login']) . "' THEN '" . $this->esc($r['surnameNew']) . "'\n";
        }
        $sql .= "    END,\n";
        $sql .= "    $nameColumn = CASE username\n";
        foreach ($rows as $r) {
            $sql .= "        WHEN '" . $this->esc($r['login']) . "' THEN '" . $this->esc($r['nameNew']) . "'\n";
        }
        $sql .= "    END\n";
        $sql .= "WHERE username IN (" . $this->inList($rows->pluck('login')) . ");\n\n";

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
