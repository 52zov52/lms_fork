<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Нагрузочный сидер для вкладки «Управление контингентом → Синхронизация с Moodle».
 *
 * Создаёт LOAD_SEED_COUNT (по умолчанию 3000) студентов в базе ЛК:
 * users + users_data (ФИО раздельно) + student_ticket_numbers + group_student.
 *
 * Запуск (НЕ входит в DatabaseSeeder, чтобы не замедлять обычный сид):
 *   php artisan db:seed --class=ContingentLoadSeeder
 *   LOAD_SEED_COUNT=5000 php artisan db:seed --class=ContingentLoadSeeder
 *
 * После сида CSV для Moodle генерируется командой:
 *   php artisan moodle:test-csv --out=storage/app/moodle_3000.csv
 *
 * Сидер идемпотентен: повторный запуск пропускается, если нагрузочные
 * студенты уже созданы (проверка по маске email).
 */
class ContingentLoadSeeder extends Seeder
{
    private const CHUNK = 500;

    private const TICKET_START = 200001;

    private const EMAIL_DOMAIN = 'load.test';

    public function run(): void
    {
        $count = max(1, (int) env('LOAD_SEED_COUNT', 3000));

        // Справочники и учебные планы — переиспользуем ContingentSeeder.
        $this->call(ContingentSeeder::class);

        $existing = DB::table('users')->where('email', 'like', '%@' . self::EMAIL_DOMAIN)->count();
        if ($existing >= $count) {
            $this->command->warn("Нагрузочные студенты уже созданы ({$existing} шт.), пропуск.");
            return;
        }
        $toCreate = $count - $existing;

        $groups = $this->ensureLoadGroups();
        $groupIds = $groups->pluck('id')->all();

        mt_srand(20260908);

        $passwordHash = Hash::make('password');
        $now = Carbon::now()->toDateTimeString();

        $this->command->info("Создание {$toCreate} нагрузочных студентов…");

        $created = 0;
        $ticket = self::TICKET_START + $existing;
        $bar = $this->command->getOutput()->createProgressBar((int) ceil($toCreate / self::CHUNK));

        while ($created < $toCreate) {
            $size = min(self::CHUNK, $toCreate - $created);

            // ── users ──
            $userRows = [];
            for ($i = 0; $i < $size; $i++) {
                $n = $existing + $created + $i;
                $userRows[] = [
                    'name' => 'loadstudent' . $n,
                    'email' => 'loadstudent' . $n . '@' . self::EMAIL_DOMAIN,
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
                ->limit($size)
                ->pluck('id')
                ->reverse()
                ->values()
                ->all();

            // ── users_data + tickets + group links ──
            $dataRows = [];
            $ticketRows = [];
            $linkRows = [];
            foreach ($ids as $k => $userId) {
                [$last, $first, $middle, $sex] = $this->randomFio();
                $ticketNumber = $ticket++;
                $dataRows[] = [
                    'last_name' => $last,
                    'first_name' => $first,
                    'middle_name' => $middle,
                    'birthday' => '2003-04-17',
                    'birthplace' => 'г. Ростов-на-Дону',
                    'sex' => $sex,
                    'document_id' => 1,
                    'passport_series' => '6001',
                    'passport_number' => (string) (300000 + ($ticketNumber % 100000)),
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
                    'user_id' => $userId,
                    'external_user_data_id' => $userId,
                    'external_user_id' => $userId,
                    'is_deleted' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $ticketRows[] = [
                    'user_id' => $userId,
                    'ticket_number' => $ticketNumber,
                    'external_id' => $ticketNumber,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $linkRows[] = [
                    'group_id' => $groupIds[($created + $k) % count($groupIds)],
                    'user_id' => $userId,
                    'external_id' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('users_data')->insert($dataRows);
            DB::table('student_ticket_numbers')->insert($ticketRows);
            DB::table('group_student')->insert($linkRows);

            $created += $size;
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Готово: всего нагрузочных студентов — " . ($existing + $created) . ".");
    }

    /**
     * 12 нагрузочных групп с датами, проходящими фильтры getLkStudents():
     * year_completion >= сегодня, admission не старше 6 лет, имя не с цифры.
     *
     * @return \Illuminate\Support\Collection<int, Group>
     */
    private function ensureLoadGroups()
    {
        $studyPlanId = DB::table('study_plans')->where('id', 1)->value('id') ?? 1;
        $workPlanId = DB::table('work_study_plans')->where('id', 1)->value('id') ?? 1;

        $prefixes = ['ТГ', 'ПГ', 'МГ', 'СГ'];
        $ids = [];
        $i = 0;
        foreach ($prefixes as $prefix) {
            for ($k = 1; $k <= 3; $k++) {
                $i++;
                $course = (($i - 1) % 4) + 1;
                $admission = Carbon::now()->subYears($course - 1)->month(9)->day(1);
                $completion = (clone $admission)->addYears(4)->month(6)->day(30);
                $name = sprintf('%s-24 Н%d', $prefix, $k);

                $group = Group::firstOrCreate(
                    ['group_name' => $name],
                    [
                        'course' => $course,
                        'year_admission' => $admission->toDateString(),
                        'year_completion' => $completion->toDateString(),
                        'study_plan_id' => $studyPlanId,
                        'work_study_plan_id' => $workPlanId,
                        'branch_id' => 1,
                        'external_id' => 900000 + $i,
                    ]
                );
                $ids[] = $group->id;
            }
        }

        return Group::whereIn('id', $ids)->orderBy('group_name')->get();
    }

    /**
     * Случайное ФИО: [фамилия, имя, отчество, пол].
     * В пуле есть ё, двойные фамилии через дефис, редкий кейс без отчества.
     *
     * @return array{0: string, 1: string, 2: string, 3: int}
     */
    private function randomFio(): array
    {
        static $lasts = [
            'Иванов', 'Петров', 'Сидоров', 'Смирнов', 'Кузнецов', 'Попов', 'Соколов',
            'Михайлов', 'Фёдоров', 'Семёнов', 'Павлов', 'Козлов', 'Морозов', 'Новиков',
            'Волков', 'Лебедев', 'Соловьёв', 'Богданов', 'Воробьёв', 'Андреев',
            'Салтыков-Щедрин', 'Петров-Водкин', 'Соколов-Микитов',
            'Иванова', 'Петрова', 'Смирнова', 'Кузнецова', 'Попова', 'Соколова',
            'Фёдорова', 'Морозова', 'Новикова', 'Волкова', 'Лебедева', 'Соловьёва',
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

        $isFemale = (bool) mt_rand(0, 1);
        $last = $lasts[mt_rand(0, count($lasts) - 1)];
        $first = $isFemale
            ? $firstsF[mt_rand(0, count($firstsF) - 1)]
            : $firstsM[mt_rand(0, count($firstsM) - 1)];
        // ~3% студентов без отчества — краевой кейс для сверки.
        $middle = mt_rand(1, 100) <= 3
            ? ''
            : ($isFemale
                ? $middlesF[mt_rand(0, count($middlesF) - 1)]
                : $middlesM[mt_rand(0, count($middlesM) - 1)]);

        return [$last, $first, $middle, $isFemale ? 0 : 1];
    }
}
