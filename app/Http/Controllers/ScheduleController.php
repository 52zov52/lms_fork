<?php

namespace App\Http\Controllers;

use App\Models\GroupStudent;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use XBase\TableReader;

class ScheduleController extends Controller
{
    public $daysOfWeek = [
        ['en' => 'Mon', 'ru' => 'Понедельник'],
        ['en' => 'Tue', 'ru' => 'Вторник'],
        ['en' => 'Wed', 'ru' => 'Среда'],
        ['en' => 'Thu', 'ru' => 'Четверг'],
        ['en' => 'Fri', 'ru' => 'Пятница'],
        ['en' => 'Sat', 'ru' => 'Суббота'],
        ['en' => 'Sun', 'ru' => 'Воскресенье'],
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachers(): object
    {
        return Schedule::select('teacher')->distinct()->orderBy('teacher', 'ASC')->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeachersByLastName($fio, $alternative_fio): object
    {
        return Schedule::select('teacher')
            ->where('teacher', '=', $fio)
            ->orWhere('teacher', '=', $alternative_fio)
            ->distinct()
            ->orderBy('teacher', 'ASC')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTeacherChair($fio): string
    {
        return Schedule::select('chair')->where('teacher', '=', $fio)->first()->chair;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getGroups(): object
    {
        return Schedule::select('group')->distinct()->orderBy('group', 'ASC')->get();
    }

    public function getlastUpdate(): string
    {
        $schedule = Schedule::select('created_at')->latest('created_at')->first();

        return (is_null($schedule)) ? '' : Carbon::parse($schedule->created_at)->format('d.m.Y H:i:s');
    }

    /**
     * Проверка, что скачанное действительно DBF (dBase III/IV/FoxPro), а не HTML и не архив.
     */
    private function assertLooksLikeDbf(string $body, string $contentType = ''): void
    {
        $head = substr($body, 0, 4);
        $firstByte = ord($body[0]);
        $validVersions = [0x02, 0x03, 0x04, 0x05, 0x30, 0x31, 0x32, 0x83, 0x8B, 0x8E, 0xF5, 0xFB];

        $looksHtml = preg_match('/^\s*(<|\xEF\xBB\xBF<)/', substr($body, 0, 20)) === 1;
        $looksZip = $head === "PK\x03\x04";

        if ($looksHtml || $looksZip || !in_array($firstByte, $validVersions, true) || strlen($body) < 100) {
            $preview = preg_replace('/\s+/u', ' ', mb_substr(strip_tags(mb_convert_encoding(substr($body, 0, 400), 'UTF-8', 'UTF-8, CP1251')), 0, 200));

            throw new \RuntimeException(
                'По ссылке расписания пришёл не DBF-файл (Content-Type: ' . ($contentType ?: 'не указан')
                . ', размер ' . strlen($body) . ' байт, начало: «' . $preview . '»). '
                . 'Возможно, сайт iubip.ru заблокировал запрос с этого сервера или ссылка изменилась.'
            );
        }
    }

    /**
     * Запасной разбор DBF без библиотеки: читает заголовок и записи по спецификации dBase.
     * Нужен для файлов, у которых заголовок библиотека hisamu/php-xbase не принимает
     * («Wrong fieldCount calculation»: нет терминатора 0x0D, лишние байты, нестандартная длина заголовка).
     *
     * @return \Generator<array<string, string>> записи вида ['LES' => '1', 'NAME' => '...']
     */
    private function readDbfFallback(string $path, string $encoding = 'CP1251'): \Generator
    {
        $fh = fopen($path, 'rb');
        if ($fh === false) {
            throw new \RuntimeException('Не удалось открыть файл расписания: ' . $path);
        }

        try {
            $header = fread($fh, 32);
            if (strlen($header) < 32) {
                throw new \RuntimeException('Файл расписания слишком короткий для DBF.');
            }

            $recordLength = unpack('v', substr($header, 10, 2))[1];
            $headerLength = unpack('v', substr($header, 8, 2))[1];

            // поля: по 32 байта до байта 0x0D
            $fields = [];
            $offset = 1; // первый байт записи - признак удаления
            while (!feof($fh)) {
                $chunk = fread($fh, 32);
                if ($chunk === false || strlen($chunk) < 32 || $chunk[0] === "\x0D") {
                    break;
                }

                $name = rtrim(substr($chunk, 0, 11), "\0 ");
                $length = ord($chunk[16]);
                $fields[] = ['name' => strtoupper($name), 'type' => $chunk[11], 'offset' => $offset, 'length' => $length];
                $offset += $length;
            }

            if (!$fields || $recordLength < 2) {
                throw new \RuntimeException('В заголовке DBF не найдено ни одного поля.');
            }

            fseek($fh, $headerLength);

            while (($raw = fread($fh, $recordLength)) !== false && strlen($raw) === $recordLength) {
                if ($raw[0] === '*') { // удалённая запись
                    continue;
                }

                $row = [];
                foreach ($fields as $f) {
                    if ($f['type'] === 'M' || $f['type'] === 'G' || $f['type'] === 'B') {
                        continue;
                    }
                    $value = substr($raw, $f['offset'], $f['length']);
                    $row[$f['name']] = trim(mb_convert_encoding($value, 'UTF-8', $encoding));
                }

                yield $row;
            }
        } finally {
            fclose($fh);
        }
    }

    /**
     * Все записи расписания как массивы: сначала через библиотеку, при ошибке чтения заголовка - запасным разбором.
     *
     * @return \Generator<array<string, string>>
     */
    private function readScheduleRecords(string $path): \Generator
    {
        $columns = ['LES', 'NAME', 'DATE', 'GROUP', 'COURSE', 'DAY', 'WEEK', 'AUD', 'SUBJECT', 'SUBJ_TYPE', 'CAFEDRA'];

        try {
            $table = new TableReader($path, ['encoding' => 'cp1251']);
        } catch (\LogicException $e) {
            \Illuminate\Support\Facades\Log::warning('Расписание: библиотека не прочитала заголовок DBF (' . $e->getMessage() . '), используется запасной разбор.');

            yield from $this->readDbfFallback($path);
            return;
        }

        while ($record = $table->nextRecord()) {
            $row = [];
            foreach ($columns as $column) {
                $row[$column] = (string) $record->get($column);
            }

            yield $row;
        }
    }

    /**
     * Скачивает DBF с сайта в storage/app/public/schedule/RASP.DBF и возвращает путь к файлу.
     * Вне production, если скачать не удалось, а файл уже лежит в этой папке (например, положили вручную),
     * берётся он - удобно для локальной разработки.
     */
    private function fetchScheduleFile(): string
    {
        $url = 'https://iubip.ru/upload/iblock/ab7/vr7vuzn1u41ly0mmcs6tah16c2ytu59k.DBF';
        $relative = 'schedule/RASP.DBF';

        try {
            try {
                $request = fn(array $cookies = []) => Http::timeout(120)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; LMS schedule loader)'])
                    ->withHeaders($cookies ? ['Cookie' => implode('; ', $cookies)] : [])
                    ->get($url);

                $response = $request();

                // Защита хостинга Beget: первый ответ - страница со скриптом, который ставит cookie beget=begetok
                // и перезагружает страницу. Повторяем запрос с этой cookie, как это сделал бы браузер.
                if (str_contains($response->body(), 'beget=begetok')) {
                    $response = $request(['beget=begetok']);
                }
            } catch (ConnectionException $e) {
                // причина (DNS, SSL-сертификат, таймаут, нет выхода в интернет) попадёт в лог и в сообщение
                throw new \RuntimeException('Не удалось подключиться к iubip.ru: ' . $e->getMessage(), 0, $e);
            }

            if (!$response->successful()) {
                throw new \RuntimeException('Сайт iubip.ru вернул HTTP ' . $response->status() . ' при загрузке файла расписания (возможно, ссылка на файл изменилась).');
            }

            $body = $response->body();

            if ($body === '') {
                throw new \RuntimeException('Файл расписания с сайта пустой.');
            }

            // Вместо DBF сайт может отдать страницу-заглушку (антибот, ошибка, редирект на HTML): такой файл
            // нельзя сохранять поверх рабочего, иначе разбор падает с «Wrong fieldCount calculation».
            $this->assertLooksLikeDbf($body, (string) $response->header('Content-Type'));

            Storage::disk('public')->put($relative, $body);
        } catch (\RuntimeException $e) {
            // Ранее сохранённый файл годится, только если это настоящий DBF (раньше сюда могла попасть HTML-заглушка)
            $cachedOk = false;
            if (Storage::disk('public')->exists($relative)) {
                try {
                    $this->assertLooksLikeDbf(Storage::disk('public')->get($relative));
                    $cachedOk = true;
                } catch (\RuntimeException $bad) {
                    Storage::disk('public')->delete($relative);
                }
            }

            if (!app()->isProduction() && $cachedOk) {
                \Illuminate\Support\Facades\Log::warning('Расписание: ' . $e->getMessage() . ' Используется ранее сохранённый файл ' . $relative);
            } else {
                throw $e;
            }
        }

        return Storage::disk('public')->path($relative);
    }

    /**
     * Приводит название группы к виду, в котором её пишут в DBF расписания:
     * без дефисов и пробелов, без префикса архивной группы «яв26.» и без суффикса-года «К1Л1(9) 2024».
     * «ЭДК-203» -> «эдк203», «К1Л1(9) 2024» -> «к1л1(9)», «ЭМ-101 2026» -> «эм101».
     */
    public static function normalizeGroupName(string $name): string
    {
        $n = mb_strtolower(trim($name));
        $n = preg_replace('/^яв\d{2}\./u', '', $n);
        $n = preg_replace('/\s+\d{4}$/u', '', $n);

        return preg_replace('/[\s\-_]+/u', '', $n);
    }

    /**
     * Названия групп из расписания, соответствующие группе LMS.
     *
     * @return string[]
     */
    public function resolveScheduleGroups(string $lmsGroupName): array
    {
        $target = self::normalizeGroupName($lmsGroupName);

        if ($target === '') {
            return [];
        }

        return Schedule::select('group')->distinct()->pluck('group')
            ->filter(fn($g) => self::normalizeGroupName((string) $g) === $target)
            ->values()
            ->all();
    }

    /**
     * Название текущей группы студента: последняя группа, срок обучения которой ещё не закончился;
     * если таких нет - просто последняя (например, у выпускника).
     */
    public function currentGroupName(int $userId): ?string
    {
        $groups = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
            ->where('group_student.user_id', '=', $userId)
            ->orderBy('group_student.id', 'desc')
            ->get(['groups.group_name', 'groups.year_completion']);

        $today = now()->format('Y-m-d');

        $active = $groups->first(fn($g) => is_null($g->year_completion) || $g->year_completion >= $today);

        return ($active ?? $groups->first())?->group_name;
    }

    /**
     * Загрузка расписания из DBF-файла с сайта.
     *
     * Сначала файл целиком читается и проверяется в памяти, и только потом, в одной транзакции,
     * старое расписание заменяется новым: если что-то пойдёт не так, прежнее расписание остаётся нетронутым
     * (раньше таблица очищалась до загрузки, и при ошибке на середине расписание оставалось неполным).
     */
    public function uploadSchedule(): void
    {
        // номер пары => время
        $lessonTimes = [
            1 => '08:20-09:50',
            2 => '10:00-11:30',
            3 => '11:40-13:10',
            4 => '13:30-15:00',
            5 => '15:10-16:40',
            6 => '17:00-18:30',
            7 => '18:40-20:10',
            8 => '20:20-21:50',
        ];

        $path = $this->fetchScheduleFile();


        $now = now();
        $rows = [];
        $skipped = 0;

        foreach ($this->readScheduleRecords($path) as $record) {
            $lessonNumber = (int) trim((string) ($record['LES'] ?? ''));

            if (!isset($lessonTimes[$lessonNumber])) {
                $skipped++;
                continue;
            }

            if (trim((string) ($record['DATE'] ?? '')) === '') {
                $skipped++;
                continue;
            }

            $teacher = trim(preg_replace('/\s+/', ' ', (string) ($record['NAME'] ?? '')));
            $date = Carbon::parse($record['DATE'])->format('Y-m-d');

            foreach (explode(',', (string) ($record['GROUP'] ?? '')) as $group) {
                $group = trim(preg_replace('/\s+/', ' ', $group));

                if ($group === '') {
                    continue;
                }

                $rows[] = [
                    'group' => mb_substr($group, 0, 100),
                    'course' => mb_substr(trim((string) ($record['COURSE'] ?? '')), 0, 50),
                    'lesson_number' => $lessonNumber,
                    'day' => mb_substr(trim((string) ($record['DAY'] ?? '')), 0, 50),
                    'week' => mb_substr(trim((string) ($record['WEEK'] ?? '')), 0, 50),
                    'date' => $date,
                    'time' => $lessonTimes[$lessonNumber],
                    'lesson_place' => mb_substr(trim((string) ($record['AUD'] ?? '')), 0, 255),
                    'discipline' => mb_substr(trim((string) ($record['SUBJECT'] ?? '')), 0, 255),
                    'lesson_type' => mb_substr(trim((string) ($record['SUBJ_TYPE'] ?? '')), 0, 255),
                    'teacher' => mb_substr($teacher, 0, 255),
                    'chair' => mb_substr(trim((string) ($record['CAFEDRA'] ?? '')), 0, 255),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (count($rows) === 0) {
            throw new \RuntimeException('В файле расписания нет ни одной записи, старое расписание оставлено без изменений.');
        }

        DB::transaction(function () use ($rows) {
            DB::table('schedules')->delete();

            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('schedules')->insert($chunk);
            }
        });

        if ($skipped > 0) {
            \Illuminate\Support\Facades\Log::warning("Загрузка расписания: пропущено записей с неизвестным номером пары: $skipped");
        }
    }

    public function getWeeksRange(): array
    {
        $weeks = Schedule::select('week', 'date')->distinct()->orderBy('week', 'ASC')->orderBy('date', 'ASC')->get()->toArray();

        $uniqueWeeks = array_unique(array_column($weeks, 'week'));
        $weeksRange = array();

        foreach ($uniqueWeeks as $week) {
            $dateRange = $this->getDataByWeek($weeks, $week);

            $dates = array_column($dateRange, 'date');
            $min = min($dates);
            $max = max($dates);

            array_push($weeksRange, ['date' => Carbon::parse($min)->format('d.m.Y') . '-' . Carbon::parse($max)->format('d.m.Y')]);
        }

        asort($weeksRange);

        return $weeksRange;
    }

    public function getCurrentSchedule($date, $teacher): array
    {
        $currentSchedule = array();
        $schedule = array();

        $now = (is_null($date)) ? Carbon::now() : Carbon::parse($date);
        $weekStartDate = $now->startOfWeek()->format('Y-m-d');
        $weekEndDate = $now->endOfWeek()->format('Y-m-d');

        if (Auth::user()->hasRole('Student')) {
            // У студента может не быть группы (например, на локальной БД) - тогда расписание пустое
            $groupName = $this->currentGroupName((int) Auth::user()->id);
            $scheduleGroups = $groupName ? $this->resolveScheduleGroups($groupName) : [];

            if (count($scheduleGroups) > 0) {
                $schedule = Schedule::where('date', '>=', $weekStartDate)
                    ->where('date', '<=', $weekEndDate)
                    ->whereIn('group', $scheduleGroups)
                    ->orderBy('date', 'ASC')
                    ->orderBy('lesson_number', 'ASC')
                    ->get()->toArray();
            }
        } else if (Auth::user()->hasRole('Teacher')) {

            if (is_null($teacher)) {
                $fio = Auth::user()->userData->last_name . ' ' . mb_substr(Auth::user()->userData->first_name, 0, 1) . '.' . mb_substr(Auth::user()->userData->middle_name, 0, 1) . '.';
            } else {
                $fio = $teacher;
            }

            $schedule = Schedule::where('date', '>=', $weekStartDate)
                ->where('date', '<=', $weekEndDate)
                ->where('teacher', '=', $fio)
                ->orderBy('date', 'ASC')
                ->orderBy('lesson_number', 'ASC')
                ->get()->toArray();
        } else {
        }

        $daysOfLesson = array_unique(array_column($schedule, 'date'));

        foreach ($daysOfLesson as $dayOfLesson) {
            $data = array();

            $scheduleByDayOfLesson = $this->getScheduleByDayOfLesson($schedule, $dayOfLesson);
            $lessonNumbers = array_unique(array_column($scheduleByDayOfLesson, 'lesson_number'));

            foreach ($lessonNumbers as $lessonNumber) {
                $scheduleByLessonNumber = $this->getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber);

                $id = array_unique(array_column($scheduleByLessonNumber, 'id'));
                $times = array_unique(array_column($scheduleByLessonNumber, 'time'));
                $disciplines = array_unique(array_column($scheduleByLessonNumber, 'discipline'));
                $lessonsPlace = array_unique(array_column($scheduleByLessonNumber, 'lesson_place'));
                $lessonsType = array_unique(array_column($scheduleByLessonNumber, 'lesson_type'));

                array_push($data, [
                    'id' => $id[0],
                    'lesson_number' => $lessonNumber,
                    'time' => $times[0],
                    'discipline' => $disciplines[0],
                    'lesson_place' => $lessonsPlace[0],
                    'lesson_type' => $lessonsType[0],
                    'data' => $scheduleByLessonNumber
                ]);
            }

            array_push($currentSchedule, [
                'day' => Carbon::parse($dayOfLesson)->shortEnglishDayOfWeek,
                'date' => $dayOfLesson,
                'schedule' => array('data' => $data)
            ]);

            unset($data);
        }

        return $currentSchedule;
    }

    public function getScheduleByTeacher($fio): array
    {
        $teacherSchedule = array();

        $schedule = Schedule::where('teacher', '=', $fio)
            ->orderBy('date', 'ASC')
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();

        $daysOfLesson = array_unique(array_column($schedule, 'date'));

        foreach ($daysOfLesson as $dayOfLesson) {
            $data = array();

            $scheduleByDayOfLesson = $this->getScheduleByDayOfLesson($schedule, $dayOfLesson);
            $lessonNumbers = array_unique(array_column($scheduleByDayOfLesson, 'lesson_number'));

            foreach ($lessonNumbers as $lessonNumber) {
                $scheduleByLessonNumber = $this->getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber);

                $times = array_unique(array_column($scheduleByLessonNumber, 'time'));
                $disciplines = array_unique(array_column($scheduleByLessonNumber, 'discipline'));
                $lessonsPlace = array_unique(array_column($scheduleByLessonNumber, 'lesson_place'));
                $lessonsType = array_unique(array_column($scheduleByLessonNumber, 'lesson_type'));

                array_push($data, [
                    'lesson_number' => $lessonNumber,
                    'time' => $times[0],
                    'discipline' => $disciplines[0],
                    'lesson_place' => $lessonsPlace[0],
                    'lesson_type' => $lessonsType[0],
                    'data' => $scheduleByLessonNumber
                ]);
            }

            array_push($teacherSchedule, [
                'day' => Carbon::parse($dayOfLesson)->shortDayName,
                'date' => $dayOfLesson,
                'schedule' => array('data' => $data)
            ]);

            unset($data);
        }

        return $teacherSchedule;
    }

    public function getScheduleByGroup($group): array
    {
        $groupSchedule = array();

        $schedule = Schedule::where('group', '=', $group)
            ->orderBy('date', 'ASC')
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();

        $weeksOfLesson = array_unique(array_column($schedule, 'week'));

        foreach ($weeksOfLesson as $weekOfLesson) {
            $data = array();

            $daysOfWeek = $this->getScheduleByWeekAndGroup($weekOfLesson, $group);

            foreach ($daysOfWeek as $dayOfWeek) {
                $scheduleByDayAndGroup = $this->getScheduleByDayAndGroup($dayOfWeek->date, $group);
                $lessonNumbers = array_unique(array_column($scheduleByDayAndGroup, 'lesson_number'));

                $lessons = array();

                foreach ($lessonNumbers as $lessonNumber) {
                    $scheduleByLessonNumber = $this->getScheduleByLessonNumber($scheduleByDayAndGroup, $lessonNumber);

                    $times = array_unique(array_column($scheduleByLessonNumber, 'time'));
                    $disciplines = array_unique(array_column($scheduleByLessonNumber, 'discipline'));
                    $teachers = array_unique(array_column($scheduleByLessonNumber, 'teacher'));
                    $lessonsPlace = array_unique(array_column($scheduleByLessonNumber, 'lesson_place'));
                    $lessonsType = array_unique(array_column($scheduleByLessonNumber, 'lesson_type'));

                    array_push($lessons, [
                        'lesson_number' => $lessonNumber,
                        'time' => $times[0],
                        'discipline' => $disciplines[0],
                        'teacher' => $teachers[0],
                        'lesson_place' => $lessonsPlace[0],
                        'lesson_type' => $lessonsType[0],
                    ]);
                }

                array_push($data, [
                    'day' => Carbon::parse($dayOfWeek->date)->shortDayName,
                    'date' => Carbon::parse($dayOfWeek->date)->format('d/m'),
                    'lessons' => $lessons
                ]);
            }

            array_push($groupSchedule, [
                'week' => $weekOfLesson,
                'data' => $data
            ]);

            unset($data);
        }

        return $groupSchedule;
    }

    public function getScheduleByWeek($week): array
    {
        $weekSchedule = array();

        $weekRange = explode('-', $week);

        $schedule = Schedule::where('date', '>=', Carbon::parse($weekRange[0])->format('Y-m-d'))
            ->where('date', '<=', Carbon::parse($weekRange[1])->format('Y-m-d'))
            ->orderBy('date', 'ASC')
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();

        $daysOfLesson = array_unique(array_column($schedule, 'date'));

        foreach ($daysOfLesson as $dayOfLesson) {
            $data = array();

            $scheduleByDayOfLesson = $this->getScheduleByDayOfLesson($schedule, $dayOfLesson);
            $lessonNumbers = array_unique(array_column($scheduleByDayOfLesson, 'lesson_number'));

            foreach ($lessonNumbers as $lessonNumber) {
                $scheduleByLessonNumber = $this->getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber);

                $times = array_unique(array_column($scheduleByLessonNumber, 'time'));
                $disciplines = array_unique(array_column($scheduleByLessonNumber, 'discipline'));
                $lessonsPlace = array_unique(array_column($scheduleByLessonNumber, 'lesson_place'));
                $lessonsType = array_unique(array_column($scheduleByLessonNumber, 'lesson_type'));

                array_push($data, [
                    'lesson_number' => $lessonNumber,
                    'time' => $times[0],
                    'discipline' => $disciplines[0],
                    'lesson_place' => $lessonsPlace[0],
                    'lesson_type' => $lessonsType[0],
                    'data' => $scheduleByLessonNumber
                ]);
            }

            array_push($weekSchedule, [
                'day' => Carbon::parse($dayOfLesson)->shortDayName,
                'date' => $dayOfLesson,
                'schedule' => array('data' => $data)
            ]);

            unset($data);
        }

        return $weekSchedule;
    }

    public function getScheduleByDayOfLesson($schedule, $dayOfLesson): array
    {
        $scheduleByDayOfLesson = array();

        foreach ($schedule as $value) {
            if ($value['date'] == $dayOfLesson) {
                array_push($scheduleByDayOfLesson, [
                    'id' => $value['id'],
                    'group' => $value['group'],
                    'course' => $value['course'],
                    'lesson_number' => $value['lesson_number'],
                    'date' => $value['date'],
                    'time' => $value['time'],
                    'lesson_place' => $value['lesson_place'],
                    'discipline' => $value['discipline'],
                    'teacher' => $value['teacher'],
                    'lesson_type' => $value['lesson_type']
                ]);
            }
        }

        return $scheduleByDayOfLesson;
    }

    public function getScheduleByLessonNumber($scheduleByDayOfLesson, $lessonNumber)
    {
        $scheduleByLessonNumber = array();

        foreach ($scheduleByDayOfLesson as $value) {
            if ($value['lesson_number'] == $lessonNumber) {
                array_push($scheduleByLessonNumber, [
                    'id' => $value['id'],
                    'group' => $value['group'],
                    'course' => $value['course'],
                    'lesson_number' => $value['lesson_number'],
                    'date' => $value['date'],
                    'time' => $value['time'],
                    'lesson_place' => $value['lesson_place'],
                    'discipline' => $value['discipline'],
                    'teacher' => $value['teacher'],
                    'lesson_type' => $value['lesson_type']
                ]);
            }
        }

        return $scheduleByLessonNumber;
    }

    private function getDataByWeek($data, $week): array
    {
        $dateRange = array();

        foreach ($data as $value) {
            if ($value['week'] == $week) {
                array_push($dateRange, [
                    'week' => $value['week'],
                    'date' => $value['date']
                ]);
            }
        }

        return $dateRange;
    }

    public function getScheduleByWeekAndGroup($week, $group): object
    {
        return Schedule::select('date')
            ->where('week', '=', $week)
            ->where('group', '=', $group)
            ->orderBy('date', 'ASC')
            ->distinct()
            ->get();
    }

    public function getScheduleByDayAndGroup($day, $group): array
    {
        return Schedule::where('date', '=', $day)
            ->where('group', '=', $group)
            ->orderBy('lesson_number', 'ASC')
            ->get()->toArray();
    }
}