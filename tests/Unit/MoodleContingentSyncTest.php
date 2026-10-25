<?php

namespace Tests\Unit;

use App\Livewire\Employee\MoodleContingentSync;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Тесты бизнес-логики управления контингентом.
 *
 * Тестируются приватные методы через ReflectionMethod:
 *   - esc()             — экранирование SQL-значений
 *   - commentSafe()     — очистка SQL-комментариев
 *   - parseMoodleCsv()  — парсинг CSV-файла Moodle
 *   - compare()         — сравнение данных ЛК и Moodle
 *   - buildSql()        — генерация SQL-запроса
 */
class MoodleContingentSyncTest extends TestCase
{
    private MoodleContingentSync $component;

    protected function setUp(): void
    {
        parent::setUp();
        $this->component = new MoodleContingentSync();
    }

    /**
     * Вызов приватного метода через Reflection.
     */
    private function callPrivate(string $method, array $args = []): mixed
    {
        $ref = new \ReflectionMethod(MoodleContingentSync::class, $method);
        $ref->setAccessible(true);
        return $ref->invokeArgs($this->component, $args);
    }

    /**
     * Создание временного CSV-файла.
     */
    private function writeTempCsv(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'csv_');
        file_put_contents($path, $content);
        return $path;
    }

    /**
     * Создание тестового массива студента.
     */
    private function makeStudent(string $login, string $last, string $first, string $group, string $middle = ''): array
    {
        return [
            $login => [
                'login' => $login,
                'last_name' => $last,
                'first_name' => $first,
                'middle_name' => $middle,
                'email' => "{$login}@test.ru",
                'group' => $group,
            ],
        ];
    }

    /**
     * Прямой вызов compare() с сбросом состояния.
     */
    private function compareDirect(array $lk, array $moodle): array
    {
        $this->setChanges([]);
        $this->component->selected = [];
        $this->component->stats = ['enroll' => 0, 'expel' => 0, 'fio' => 0, 'transfer' => 0, 'relink' => 0];
        $this->callPrivate('compare', [$lk, $moodle]);
        return $this->getChanges();
    }

    /**
     * Настройка состояния и вызов buildSql().
     */
    private function buildSqlWithChanges(array $changes): string
    {
        $this->setChanges([]);
        $this->component->selected = [];
        $this->component->stats = ['enroll' => 0, 'expel' => 0, 'fio' => 0, 'transfer' => 0, 'relink' => 0];

        foreach ($changes as $c) {
            $id = count($this->getChanges());
            $c['id'] = $id;
            $all = $this->getChanges();
            $all[] = $c;
            $this->setChanges($all);
            $this->component->selected[$id] = true;
            $this->component->stats[$c['type']]++;
        }

        return $this->callPrivate('buildSql');
    }

    /**
     * Доступ к защищённому свойству $changes (не сериализуется Livewire).
     */
    private function getChanges(): array
    {
        $prop = new \ReflectionProperty($this->component, 'changes');
        $prop->setAccessible(true);

        return $prop->getValue($this->component);
    }

    /**
     * Запись в защищённое свойство $changes.
     */
    private function setChanges(array $changes): void
    {
        $prop = new \ReflectionProperty($this->component, 'changes');
        $prop->setAccessible(true);
        $prop->setValue($this->component, $changes);
    }

    // ═══════════════════════════════════════════════════════════
    // esc()
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function esc_single_quote_is_doubled(): void
    {
        $this->assertSame("O''Brien", $this->callPrivate('esc', ["O'Brien"]));
    }

    #[Test]
    public function esc_backslash_is_escaped(): void
    {
        $this->assertSame('test\\\\value', $this->callPrivate('esc', ['test\\value']));
    }

    #[Test]
    public function esc_null_byte(): void
    {
        $this->assertSame('\\0', $this->callPrivate('esc', ["\0"]));
    }

    #[Test]
    public function esc_newline(): void
    {
        $this->assertSame('\\n', $this->callPrivate('esc', ["\n"]));
    }

    #[Test]
    public function esc_carriage_return(): void
    {
        $this->assertSame('\\r', $this->callPrivate('esc', ["\r"]));
    }

    #[Test]
    public function esc_ctrl_z(): void
    {
        $this->assertSame('\\Z', $this->callPrivate('esc', ["\x1a"]));
    }

    #[Test]
    public function esc_double_quote_not_escaped(): void
    {
        $this->assertSame('say "hello"', $this->callPrivate('esc', ['say "hello"']));
    }

    #[Test]
    public function esc_backtick_not_escaped(): void
    {
        $this->assertSame('`backtick`', $this->callPrivate('esc', ['`backtick`']));
    }

    #[Test]
    public function esc_clean_string_unchanged(): void
    {
        $this->assertSame('Ivan Petrov', $this->callPrivate('esc', ['Ivan Petrov']));
    }

    #[Test]
    public function esc_backslash_then_quote(): void
    {
        $input = "test\\'done";
        $expected = "test\\\\''done";
        $this->assertSame($expected, $this->callPrivate('esc', [$input]));
    }

    #[Test]
    public function esc_multiple_special_chars(): void
    {
        $input = "O'Brien\nSmith\\";
        $result = $this->callPrivate('esc', [$input]);
        $this->assertSame("O''Brien\\nSmith\\\\", $result);
    }

    // ═══════════════════════════════════════════════════════════
    // commentSafe()
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function commentSafe_removes_newlines(): void
    {
        $this->assertSame('line1 line2', $this->callPrivate('commentSafe', ["line1\nline2"]));
    }

    #[Test]
    public function commentSafe_removes_control_chars(): void
    {
        $this->assertSame('abc def', $this->callPrivate('commentSafe', ["abc\x01def"]));
    }

    #[Test]
    public function commentSafe_preserves_hyphens(): void
    {
        $this->assertSame('group--name', $this->callPrivate('commentSafe', ["group--name"]));
    }

    #[Test]
    public function commentSafe_preserves_normal_text(): void
    {
        $this->assertSame('ИТ-24 К1', $this->callPrivate('commentSafe', ['ИТ-24 К1']));
    }

    // ═══════════════════════════════════════════════════════════
    // parseMoodleCsv()
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function parseMoodleCsv_comma_delimited(): void
    {
        $csv = "st_ticket,last_name,first_name,email,group_name_fixed\n"
             . "100001,Ivanov,Ivan,iv@test.ru,ИТ-24 К1\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertArrayHasKey('100001', $result);
        $this->assertSame('Ivanov', $result['100001']['last_name']);
        $this->assertSame('Ivan', $result['100001']['first_name']);
        $this->assertSame('ИТ-24К1 (2026)', $result['100001']['group']);
    }

    #[Test]
    public function parseMoodleCsv_semicolon_delimited(): void
    {
        $csv = "st_ticket;last_name;first_name;email;group_name_fixed\n"
             . "100001;Ivanov;Ivan;iv@test.ru;ИТ-24 К1\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertArrayHasKey('100001', $result);
    }

    #[Test]
    public function parseMoodleCsv_russian_headers(): void
    {
        $csv = "логин;фамилия;имя;email;группа\n"
             . "100001;Петров;Пётр;petr@test.ru;ИТ-23 К2\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertArrayHasKey('100001', $result);
        $this->assertSame('Петров', $result['100001']['last_name']);
    }

    #[Test]
    public function parseMoodleCsv_empty_file_throws(): void
    {
        $path = $this->writeTempCsv('');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Файл пуст');
        $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);
    }

    #[Test]
    public function parseMoodleCsv_missing_column_throws(): void
    {
        $csv = "login,name,group\n100001,Ivan,ИТ-24\n";
        $path = $this->writeTempCsv($csv);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('не найдена колонка');
        $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);
    }

    #[Test]
    public function parseMoodleCsv_skips_empty_logins(): void
    {
        $csv = "st_ticket,last_name,first_name,email,group_name_fixed\n"
             . "100001,Ivanov,Ivan,iv@test.ru,ИТ-24 К1\n"
             . ",Unknown,User,unk@test.ru,ИТ-24 К1\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertCount(1, $result);
        $this->assertArrayHasKey('100001', $result);
    }

    #[Test]
    public function parseMoodleCsv_last_login_wins_on_duplicate(): void
    {
        $csv = "st_ticket,last_name,first_name,email,group_name_fixed\n"
             . "100001,Ivanov,Ivan,iv@test.ru,ИТ-24 К1\n"
             . "100001,NewSurname,NewName,new@test.ru,ИТ-23 К2\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertCount(1, $result);
        $this->assertSame('NewSurname', $result['100001']['last_name']);
    }

    #[Test]
    public function parseMoodleCsv_short_row_skipped(): void
    {
        $csv = "st_ticket,last_name,first_name,email,group_name_fixed\n"
             . "100001,Ivanov,Ivan,iv@test.ru,ИТ-24 К1\n"
             . "100002,Petrov\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertCount(1, $result);
    }

    #[Test]
    public function parseMoodleCsv_crlf_line_endings(): void
    {
        $csv = "st_ticket,last_name,first_name,email,group_name_fixed\r\n"
             . "100001,Ivanov,Ivan,iv@test.ru,ИТ-24 К1\r\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertArrayHasKey('100001', $result);
    }

    #[Test]
    public function parseMoodleCsv_with_utf8_bom(): void
    {
        $csv = "\xEF\xBB\xBFst_ticket,last_name,first_name,email,group_name_fixed\n"
             . "100001,Ivanov,Ivan,iv@test.ru,МГ-24 Н1\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertArrayHasKey('100001', $result);
        $this->assertSame('МГ-24Н1 (2026)', $result['100001']['group']);
    }

    #[Test]
    public function parseMoodleCsv_windows1251_encoding(): void
    {
        $utf8 = "st_ticket,last_name,first_name,email,group_name_fixed\n"
              . "100001,Иванов,Иван,iv@test.ru,ИТ-24 К1\n";
        $win1251 = mb_convert_encoding($utf8, 'windows-1251', 'UTF-8');
        $path = $this->writeTempCsv($win1251);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertArrayHasKey('100001', $result);
        $this->assertSame('Иванов', $result['100001']['last_name']);
    }

    // ═══════════════════════════════════════════════════════════
    // normalizeGroup()
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function normalizeGroup_adds_year_and_removes_spaces(): void
    {
        $this->assertSame('МГ-24Н1 (2026)', $this->callPrivate('normalizeGroup', ['МГ-24 Н1']));
    }

    #[Test]
    public function normalizeGroup_strips_bare_year(): void
    {
        $this->assertSame('ИТ-24К1 (2026)', $this->callPrivate('normalizeGroup', ['ИТ-24К1 2026']));
    }

    #[Test]
    public function normalizeGroup_strips_parenthesized_year(): void
    {
        $this->assertSame('ИТ-24 К1 (2026)', $this->callPrivate('normalizeGroup', ['ИТ-24 К1 (2026)']));
    }

    #[Test]
    public function normalizeGroup_leaves_already_normalized_untouched(): void
    {
        $input = 'МГ-24Н1 (2026)';
        $this->assertSame($input, $this->callPrivate('normalizeGroup', [$input]));
    }

    #[Test]
    public function normalizeGroup_handles_group_without_year(): void
    {
        $this->assertSame('ЭК-23К1 (2026)', $this->callPrivate('normalizeGroup', ['ЭК-23 К1']));
    }

    #[Test]
    public function parseMoodleCsv_normalizes_group_column(): void
    {
        $csv = "st_ticket,last_name,first_name,email,group_name_fixed\n"
             . "100001,Ivanov,Ivan,iv@test.ru,МГ-24 Н1\n";
        $path = $this->writeTempCsv($csv);

        $result = $this->callPrivate('parseMoodleCsv', [$path]);
        unlink($path);

        $this->assertSame('МГ-24Н1 (2026)', $result['100001']['group']);
    }

    // ═══════════════════════════════════════════════════════════
    // compare()
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function compare_only_lk_creates_enroll(): void
    {
        $lk = $this->makeStudent('100001', 'Ivanov', 'Ivan', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, []);

        $this->assertCount(1, $result);
        $this->assertSame('enroll', $result[0]['type']);
        $this->assertEquals('100001', $result[0]['login']);
    }

    #[Test]
    public function compare_only_moodle_creates_expel(): void
    {
        $moodle = $this->makeStudent('100001', 'Ivanov', 'Ivan', 'ИТ-24 К1');
        $result = $this->compareDirect([], $moodle);

        $this->assertCount(1, $result);
        $this->assertSame('expel', $result[0]['type']);
    }

    #[Test]
    public function compare_fio_difference(): void
    {
        $lk = $this->makeStudent('100001', 'Иванов', 'Иван', 'ИТ-24 К1');
        $moodle = $this->makeStudent('100001', 'Иванова', 'Иван', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(1, $result);
        $this->assertSame('fio', $result[0]['type']);
        $this->assertSame('Иванова', $result[0]['surnameOld']);
        $this->assertSame('Иванов', $result[0]['surnameNew']);
    }

    #[Test]
    public function compare_group_difference_creates_transfer(): void
    {
        $lk = $this->makeStudent('100001', 'Ivanov', 'Ivan', 'ИТ-23 К2');
        $moodle = $this->makeStudent('100001', 'Ivanov', 'Ivan', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(1, $result);
        $this->assertSame('transfer', $result[0]['type']);
        $this->assertSame('ИТ-24 К1', $result[0]['groupOld']);
        $this->assertSame('ИТ-23 К2', $result[0]['groupNew']);
    }

    #[Test]
    public function compare_fio_and_group_both_differ(): void
    {
        $lk = $this->makeStudent('100001', 'Иванов', 'Петр', 'ИТ-23 К2');
        $moodle = $this->makeStudent('100001', 'Иванова', 'Иван', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $types = array_column($result, 'type');
        $this->assertContains('fio', $types);
        $this->assertContains('transfer', $types);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function compare_identical_no_changes(): void
    {
        $lk = $this->makeStudent('100001', 'Ivanov', 'Ivan', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $lk);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function compare_multiple_changes(): void
    {
        $lk = [
            '100001' => ['login' => '100001', 'last_name' => 'A', 'first_name' => 'B', 'email' => 'a@test.ru', 'group' => 'Г1'],
            '100002' => ['login' => '100002', 'last_name' => 'C', 'first_name' => 'D', 'email' => 'c@test.ru', 'group' => 'Г1'],
        ];
        $moodle = [
            '100001' => ['login' => '100001', 'last_name' => 'A', 'first_name' => 'B', 'email' => 'a@test.ru', 'group' => 'Г1'],
            '100003' => ['login' => '100003', 'last_name' => 'E', 'first_name' => 'F', 'email' => 'e@test.ru', 'group' => 'Г2'],
        ];
        $result = $this->compareDirect($lk, $moodle);

        $types = array_column($result, 'type');
        $this->assertContains('enroll', $types);
        $this->assertContains('expel', $types);
        $this->assertCount(2, $result);
    }

    #[Test]
    public function compare_case_insensitive_fio(): void
    {
        $lk = $this->makeStudent('100001', 'ivanov', 'ivan', 'ИТ-24 К1');
        $moodle = $this->makeStudent('100001', 'IVANOV', 'IVAN', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(0, $result);
    }

    // ═══════════════════════════════════════════════════════════
    // buildSql()
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function buildSql_enroll_creates_insert(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'enroll', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => '', 'surnameNew' => 'Иванов',
                'nameOld' => '', 'nameNew' => 'Иван', 'groupOld' => '', 'groupNew' => 'ИТ-24 К1',
                'email' => '100001@test.ru',
            ],
        ]);

        $this->assertStringContainsString('INSERT INTO mdl_user', $sql);
        $this->assertStringContainsString("'100001'", $sql);
        $this->assertStringContainsString("'Иванов'", $sql);
    }

    #[Test]
    public function buildSql_expel_creates_delete(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'expel', 'reason' => 'test',
                'login' => '100002', 'surnameOld' => 'Петров', 'surnameNew' => '',
                'nameOld' => 'Пётр', 'nameNew' => '', 'groupOld' => 'ИТ-23 К2', 'groupNew' => '',
                'email' => '',
            ],
        ]);

        $this->assertStringContainsString('DELETE FROM mdl_cohort_members', $sql);
        $this->assertStringContainsString("'100002'", $sql);
    }

    #[Test]
    public function buildSql_fio_creates_update(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'fio', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => 'Иванова', 'surnameNew' => 'Иванов',
                'nameOld' => 'Иван', 'nameNew' => 'Иван', 'groupOld' => 'ИТ-24 К1', 'groupNew' => 'ИТ-24 К1',
                'email' => '',
            ],
        ]);

        $this->assertStringContainsString('UPDATE mdl_user SET', $sql);
        $this->assertStringContainsString('lastname', $sql);
        $this->assertStringContainsString("'Иванов'", $sql);
    }

    #[Test]
    public function buildSql_transfer_creates_delete_and_insert(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'transfer', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => 'Иванов', 'surnameNew' => 'Иванов',
                'nameOld' => 'Иван', 'nameNew' => 'Иван', 'groupOld' => 'ИТ-24 К1', 'groupNew' => 'ИТ-23 К2',
                'email' => '',
            ],
        ]);

        $this->assertStringContainsString('DELETE FROM mdl_cohort_members', $sql);
        $this->assertStringContainsString('INSERT INTO mdl_cohort_members', $sql);
    }

    #[Test]
    public function buildSql_starts_with_transaction(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'enroll', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => '', 'surnameNew' => 'A',
                'nameOld' => '', 'nameNew' => 'B', 'groupOld' => '', 'groupNew' => 'Г1',
                'email' => 'a@test.ru',
            ],
        ]);

        $this->assertStringStartsWith('START TRANSACTION;', $sql);
    }

    #[Test]
    public function buildSql_ends_with_commit(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'enroll', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => '', 'surnameNew' => 'A',
                'nameOld' => '', 'nameNew' => 'B', 'groupOld' => '', 'groupNew' => 'Г1',
                'email' => 'a@test.ru',
            ],
        ]);

        $this->assertStringContainsString("COMMIT;\n", $sql);
    }

    #[Test]
    public function buildSql_no_changes_just_transaction(): void
    {
        $sql = $this->buildSqlWithChanges([]);

        $this->assertStringContainsString('START TRANSACTION;', $sql);
        $this->assertStringContainsString('COMMIT;', $sql);
        $this->assertStringNotContainsString('INSERT', $sql);
        $this->assertStringNotContainsString('DELETE', $sql);
        $this->assertStringNotContainsString('UPDATE', $sql);
    }

    #[Test]
    public function buildSql_escapes_single_quotes_in_login(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'enroll', 'reason' => 'test',
                'login' => "100001'OR 1=1--", 'surnameOld' => '', 'surnameNew' => 'A',
                'nameOld' => '', 'nameNew' => 'B', 'groupOld' => '', 'groupNew' => 'Г1',
                'email' => 'a@test.ru',
            ],
        ]);

        $this->assertStringContainsString("'100001''OR 1=1--'", $sql);
    }

    #[Test]
    public function buildSql_includes_block_comments(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'enroll', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => '', 'surnameNew' => 'A',
                'nameOld' => '', 'nameNew' => 'B', 'groupOld' => '', 'groupNew' => 'Г1',
                'email' => 'a@test.ru',
            ],
            [
                'type' => 'expel', 'reason' => 'test',
                'login' => '100002', 'surnameOld' => 'C', 'surnameNew' => '',
                'nameOld' => 'D', 'nameNew' => '', 'groupOld' => 'Г2', 'groupNew' => '',
                'email' => '',
            ],
            [
                'type' => 'fio', 'reason' => 'test',
                'login' => '100003', 'surnameOld' => 'Old', 'surnameNew' => 'New',
                'nameOld' => 'Name', 'nameNew' => 'Name', 'groupOld' => 'Г1', 'groupNew' => 'Г1',
                'email' => '',
            ],
        ]);

        $this->assertStringContainsString('БЛОК 1', $sql);
        $this->assertStringContainsString('БЛОК 2', $sql);
        $this->assertStringContainsString('БЛОК 5', $sql);
    }

    #[Test]
    public function buildSql_empty_group_in_transfer_is_skipped(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'transfer', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => 'A', 'surnameNew' => 'A',
                'nameOld' => 'B', 'nameNew' => 'B', 'groupOld' => 'Г1', 'groupNew' => '',
                'email' => '',
            ],
        ]);

        $this->assertStringContainsString('DELETE FROM mdl_cohort_members', $sql);
        $this->assertStringNotContainsString('INSERT INTO mdl_cohort_members', $sql);
    }

    // ═══════════════════════════════════════════════════════════
    // normalizeFioWords()
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function normalizeFioWords_ignores_case_and_yo(): void
    {
        $a = $this->callPrivate('normalizeFioWords', ['Пётр ИВАНОВ']);
        $b = $this->callPrivate('normalizeFioWords', ['иванов пётр']);
        $c = $this->callPrivate('normalizeFioWords', ['Иванов Петр']);
        $this->assertSame($a, $b);
        $this->assertSame($a, $c);
    }

    #[Test]
    public function normalizeFioWords_ignores_word_order_and_spaces(): void
    {
        $a = $this->callPrivate('normalizeFioWords', ['Иванов   Иван Петрович']);
        $b = $this->callPrivate('normalizeFioWords', ['Петрович Иван Иванов']);
        $this->assertSame($a, $b);
    }

    #[Test]
    public function normalizeFioWords_treats_hyphen_as_space(): void
    {
        $a = $this->callPrivate('normalizeFioWords', ['Салтыков-Щедрин Михаил']);
        $b = $this->callPrivate('normalizeFioWords', ['Салтыков Щедрин Михаил']);
        $this->assertSame($a, $b);
    }

    // ═══════════════════════════════════════════════════════════
    // compare() с отчеством и переставленными полями Moodle
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function compare_reversed_moodle_layout_with_patronymic_no_changes(): void
    {
        // ЛК: Фамилия/Имя/Отчество раздельно.
        $lk = $this->makeStudent('100001', 'Иванов', 'Иван', 'ИТ-24 К1', 'Петрович');
        // Moodle (перевёрнутая раскладка): firstname=Фамилия, lastname=Имя Отчество.
        // В parse-массиве last_name = Moodle firstname, first_name = Moodle lastname.
        $moodle = $this->makeStudent('100001', 'Иванов', 'Иван Петрович', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function compare_swapped_moodle_fields_no_changes(): void
    {
        $lk = $this->makeStudent('100001', 'Иванов', 'Иван', 'ИТ-24 К1', 'Петрович');
        // Moodle со стандартной раскладкой: firstname=Имя Отчество, lastname=Фамилия.
        $moodle = $this->makeStudent('100001', 'Иван Петрович', 'Иванов', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function compare_missing_patronymic_in_moodle_creates_fio_with_full_name(): void
    {
        $lk = $this->makeStudent('100001', 'Иванов', 'Иван', 'ИТ-24 К1', 'Петрович');
        // В Moodle отчества нет вовсе.
        $moodle = $this->makeStudent('100001', 'Иванов', 'Иван', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(1, $result);
        $this->assertSame('fio', $result[0]['type']);
        // «Станет» должно содержать отчество — иначе оно затрётся в Moodle.
        $this->assertSame('Иван Петрович', $result[0]['nameNew']);
        $this->assertSame('Иванов', $result[0]['surnameNew']);
    }

    #[Test]
    public function compare_patronymic_only_in_one_moodle_field_still_matches(): void
    {
        $lk = $this->makeStudent('100001', 'Петрова', 'Анна', 'ИТ-24 К1', 'Сергеевна');
        // Отчество приклеено к фамилии в одном поле Moodle.
        $moodle = $this->makeStudent('100001', 'Петрова Сергеевна', 'Анна', 'ИТ-24 К1');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function compare_enroll_name_new_includes_patronymic(): void
    {
        $lk = $this->makeStudent('100001', 'Иванов', 'Иван', 'ИТ-24 К1', 'Петрович');
        $result = $this->compareDirect($lk, []);

        $this->assertCount(1, $result);
        $this->assertSame('enroll', $result[0]['type']);
        $this->assertSame('Иван Петрович', $result[0]['nameNew']);
    }

    // ═══════════════════════════════════════════════════════════
    // Категории Moodle и сопоставление по ФИО
    // ═══════════════════════════════════════════════════════════

    /**
     * Строка Moodle с категорией и исходной (пустой) группой.
     *
     * @param  string  $login     Логин
     * @param  string  $last      Moodle firstname (в CSV идёт в колонку last_name)
     * @param  string  $first     Moodle lastname
     * @param  int     $category  1 — стандарт, 2 — свободные, 3 — доп. набор
     * @param  string  $group     Исходная группа ('' — без когорты)
     */
    private function makeMoodleRow(string $login, string $last, string $first, int $category = 1, string $group = 'БАЗА (2026)'): array
    {
        return [
            $login => [
                'login' => $login,
                'last_name' => $last,
                'first_name' => $first,
                'email' => "{$login}@test.ru",
                'groupRaw' => $group,
                'group' => $group === '' ? ' (2026)' : $group,
                'category' => $category,
            ],
        ];
    }

    #[Test]
    public function compare_free_student_is_not_expelled(): void
    {
        // Категория 2: логин st* есть, когорты нет (академ. отпуск/отчислен).
        // В ЛК его нет — отчислять нельзя, в ЛК видны только активные группы.
        $moodle = $this->makeMoodleRow('st100075', 'Ким', 'Дарья', 2, '');
        $result = $this->compareDirect([], $moodle);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function compare_free_student_gets_added_to_group(): void
    {
        // Тот же студент, но он есть в ЛК: нужен перевод, а не зачисление.
        // Группа приходит из ЛК уже нормализованной (нормализация — в SQL ЛК).
        $lk = $this->makeStudent('st100075', 'Ким', 'Дарья', 'ИТ-24К1 (2026)');
        $moodle = $this->makeMoodleRow('st100075', 'Ким', 'Дарья', 2, '');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(1, $result);
        $this->assertSame('transfer', $result[0]['type']);
        $this->assertSame('', $result[0]['groupOld'], 'У студента без когорты нечего удалять');
        $this->assertSame('ИТ-24К1 (2026)', $result[0]['groupNew']);
    }

    #[Test]
    public function compare_extra_set_matches_by_fio_and_generates_relink(): void
    {
        // Доп. набор: в Moodle временный логин vr*, в ЛК настоящий st*.
        // Сопоставление только по ФИО — логины разные.
        $lk = $this->makeStudent('st113099', 'Ткаченко', 'Елена', 'К1Л1(11)', 'Георгиевна');
        $moodle = $this->makeMoodleRow('vr_elena1', 'Елена Георгиевна', 'Ткаченко', 3, 'К1Л1(11) (2026)');
        $result = $this->compareDirect($lk, $moodle);

        $relink = array_values(array_filter($result, fn ($c) => $c['type'] === 'relink'));
        $this->assertCount(1, $relink, 'Должен быть ровно один relink');
        $this->assertSame('vr_elena1', $relink[0]['login'], 'SQL работает по текущему логину Moodle');
        $this->assertSame('st113099', $relink[0]['loginNew'], 'Новый логин берётся из ЛК');

        $this->assertCount(0, array_filter($result, fn ($c) => $c['type'] === 'enroll'), 'Новый пользователь не создаётся');
        $this->assertCount(0, array_filter($result, fn ($c) => $c['type'] === 'expel'), 'Найденный по ФИО не отчисляется');
    }

    #[Test]
    public function compare_extra_set_makes_no_duplicate_transfer(): void
    {
        // У «Доп. набора» группу меняет сама relink: отдельного перевода
        // быть не должно, иначе студент попадёт в две строки с одной группой.
        $lk = $this->makeStudent('st113099', 'Ткаченко', 'Елена', 'К1Л1(11) (2026)', 'Георгиевна');
        $moodle = $this->makeMoodleRow('vr_elena1', 'Елена Георгиевна', 'Ткаченко', 3, '');
        $result = $this->compareDirect($lk, $moodle);

        $relink = array_values(array_filter($result, fn ($c) => $c['type'] === 'relink'));
        $transfer = array_values(array_filter($result, fn ($c) => $c['type'] === 'transfer'));

        $this->assertCount(1, $relink);
        $this->assertCount(0, $transfer, 'relink уже несёт смену группы');
        $this->assertSame('', $relink[0]['groupOld'], 'Был без когорты');
        $this->assertSame('К1Л1(11) (2026)', $relink[0]['groupNew']);
    }

    #[Test]
    public function compare_falls_back_from_standard_to_free_category(): void
    {
        // ФИО есть и в «Стандарте», и в «Свободных» — побеждает «Стандарт»:
        // его категория 1, и в SQL он уже числится в когорте.
        $lk = $this->makeStudent('st100500', 'Петров', 'Пётр', 'ИТ-24 К1', 'Сергеевич');
        $moodle = $this->makeMoodleRow('st100900', 'Петров', 'Пётр Сергеевич', 2, '');
        $moodle += $this->makeMoodleRow('st100901', 'Петров', 'Пётр Сергеевич', 1, 'БАЗА (2026)');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(0, array_filter($result, fn ($c) => $c['type'] === 'enroll'));

        // Побеждён кандидат из «Стандарта»: перевод из его когорты, не пустой группы.
        $transfer = array_values(array_filter($result, fn ($c) => $c['type'] === 'transfer'));
        $this->assertCount(1, $transfer);
        $this->assertSame('st100901', $transfer[0]['login']);
        $this->assertSame('БАЗА (2026)', $transfer[0]['groupOld']);
    }

    #[Test]
    public function compare_does_not_match_different_surname(): void
    {
        // Фамилии лишь похожи — сопоставлять нельзя, нужен новый пользователь.
        $lk = $this->makeStudent('st100500', 'Петров', 'Пётр', 'ИТ-24 К1');
        $moodle = $this->makeMoodleRow('vr_ivan', 'Петрова', 'Пётр', 3, '');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(1, $result);
        $this->assertSame('enroll', $result[0]['type']);
        $this->assertSame('st100500', $result[0]['login']);
    }

    #[Test]
    public function compare_ambiguous_fio_creates_enroll_and_warns(): void
    {
        // Два одинаковых ФИО в одной категории — сопоставление неоднозначно.
        $lk = $this->makeStudent('st100500', 'Сидоров', 'Сидор', 'ИТ-24 К1');
        $moodle = $this->makeMoodleRow('vr_a', 'Сидоров', 'Сидор', 3, '');
        $moodle += $this->makeMoodleRow('vr_b', 'Сидоров', 'Сидор', 3, '');
        $result = $this->compareDirect($lk, $moodle);

        $this->assertCount(1, $result);
        $this->assertSame('enroll', $result[0]['type']);
        $this->assertCount(0, array_filter($result, fn ($c) => $c['type'] === 'expel'));
        $this->assertNotEmpty($this->component->moodleWarnings, 'Неоднозначность должна попасть в предупреждения');
    }

    #[Test]
    public function buildSql_relink_updates_username_last(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'relink', 'reason' => 'test',
                'login' => 'vr_elena1', 'loginNew' => 'st113099',
                'surnameOld' => 'Елена', 'surnameNew' => 'Ткаченко',
                'nameOld' => 'Ткаченко', 'nameNew' => 'Елена Георгиевна',
                'groupOld' => '', 'groupNew' => 'К1Л1(11) (2026)', 'email' => 'a@b.ru',
            ],
        ]);

        $this->assertStringContainsString("UPDATE mdl_user SET username = 'st113099'", $sql);
        $this->assertStringContainsString("WHERE username = 'vr_elena1'", $sql);
        // Пустой старой группы нет в карте когорт — лишнего DELETE быть не должно.
        $this->assertStringNotContainsString('cohortid = NULL', $sql);
        $this->assertStringContainsString('INSERT INTO mdl_cohort_members', $sql);
        // Смена логина идёт после добавления в когорту (пользователь ещё vr).
        $this->assertGreaterThan(
            strpos($sql, 'INSERT INTO mdl_cohort_members'),
            strpos($sql, 'UPDATE mdl_user SET username'),
            'Смена логина должна идти после операций с когортами'
        );
    }

    // ═══════════════════════════════════════════════════════════
    // buildSql(): перевёрнутый INSERT и UPDATE с раскладкой
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function buildSql_enroll_uses_reversed_layout(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'enroll', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => '', 'surnameNew' => 'Иванов',
                'nameOld' => '', 'nameNew' => 'Иван Петрович', 'groupOld' => '', 'groupNew' => 'ИТ-24 К1',
                'email' => '100001@test.ru',
            ],
        ]);

        // Moodle firstname = Фамилия, lastname = «Имя Отчество».
        $this->assertMatchesRegularExpression(
            "/'100001',\s*'\S+',\s*'Иванов',\s*'Иван Петрович',\s*'100001@test\.ru'/u",
            $sql
        );
    }

    #[Test]
    public function buildSql_fio_preserves_reversed_layout(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'fio', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => 'Иванов', 'surnameNew' => 'Иванов',
                'nameOld' => 'Иван', 'nameNew' => 'Иван Петрович', 'groupOld' => 'Г1', 'groupNew' => 'Г1',
                'email' => '', 'surnameInFirstname' => true,
            ],
        ]);

        $this->assertStringContainsString('firstname = CASE username', $sql);
        $this->assertMatchesRegularExpression("/WHEN '100001' THEN 'Иванов'/u", $sql);
        $this->assertMatchesRegularExpression("/WHEN '100001' THEN 'Иван Петрович'/u", $sql);
    }

    #[Test]
    public function buildSql_fio_preserves_standard_layout(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'fio', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => 'Иванов', 'surnameNew' => 'Иванов',
                'nameOld' => 'Иван', 'nameNew' => 'Иван Петрович', 'groupOld' => 'Г1', 'groupNew' => 'Г1',
                'email' => '', 'surnameInFirstname' => false,
            ],
        ]);

        // Фамилия должна уйти в lastname, «Имя Отчество» — в firstname.
        $this->assertMatchesRegularExpression(
            "/lastname = CASE username\s+WHEN '100001' THEN 'Иванов'/u",
            $sql
        );
        $this->assertMatchesRegularExpression(
            "/firstname = CASE username\s+WHEN '100001' THEN 'Иван Петрович'/u",
            $sql
        );
    }

    #[Test]
    public function component_renders_initial_state(): void
    {
        Livewire::test(MoodleContingentSync::class)
            ->assertOk()
            ->assertSee('Скопировать запрос мудл');
    }

    #[Test]
    public function component_renders_paginated_results(): void
    {
        // Массив изменений не сериализуется в снапшот Livewire — компонент
        // достаёт его из кеша по ключу (см. свойство $changesKey и boot()).
        Cache::put('test:moodle:sync:paginated', [
            [
                'id' => 0, 'type' => 'fio', 'reason' => 'test',
                'login' => 'st200001', 'surnameOld' => 'Иванов', 'surnameNew' => 'Иванов',
                'nameOld' => 'Иван', 'nameNew' => 'Иван Петрович',
                'groupOld' => 'Г1', 'groupNew' => 'Г1', 'email' => '',
                'surnameInFirstname' => true,
            ],
            [
                'id' => 1, 'type' => 'enroll', 'reason' => 'test',
                'login' => 'st200002', 'surnameOld' => '', 'surnameNew' => 'Петров',
                'nameOld' => '', 'nameNew' => 'Пётр Петрович',
                'groupOld' => '', 'groupNew' => 'Г1', 'email' => 'st200002@test.ru',
            ],
        ], 60);

        Livewire::test(MoodleContingentSync::class)
            ->set('changesKey', 'test:moodle:sync:paginated')
            ->set('selected', [0 => true, 1 => true])
            ->set('stats', ['enroll' => 1, 'expel' => 0, 'fio' => 1, 'transfer' => 0, 'relink' => 0])
            ->set('parsed', true)
            ->assertOk()
            ->assertSee('st200001')
            ->assertSee('Иван Петрович')
            ->assertSee('Показать SQL')
            // Серверные вкладки: переход на «Зачисления» показывает её таблицу
            // и держит выбранную вкладку после обновления (пагинация).
            ->call('setTab', 'enroll')
            ->assertSee('st200002')
            ->assertSee('Пётр Петрович')
            ->assertDontSee('st200001')
            ->call('gotoPage', 'enroll', 1)
            ->assertSee('st200002')
            ->call('buildPreview')
            ->assertSee('UPDATE mdl_user SET');
    }

    #[Test]
    public function component_tab_is_server_state_and_survives_goto_page(): void
    {
        Cache::put('test:moodle:sync:tab', [
            [
                'id' => 0, 'type' => 'fio', 'reason' => 'test',
                'login' => 'st300001', 'surnameOld' => 'А', 'surnameNew' => 'Б',
                'nameOld' => 'В', 'nameNew' => 'Г Д',
                'groupOld' => 'Г1', 'groupNew' => 'Г1', 'email' => '',
            ],
            [
                'id' => 1, 'type' => 'transfer', 'reason' => 'test',
                'login' => 'st300002', 'surnameOld' => 'Е', 'surnameNew' => 'Е',
                'nameOld' => 'Ж', 'nameNew' => 'Ж',
                'groupOld' => 'Г1', 'groupNew' => 'Г2', 'email' => '',
            ],
        ], 60);

        Livewire::test(MoodleContingentSync::class)
            ->set('changesKey', 'test:moodle:sync:tab')
            ->set('selected', [0 => true, 1 => true])
            ->set('stats', ['enroll' => 0, 'expel' => 0, 'fio' => 1, 'transfer' => 1, 'relink' => 0])
            ->set('parsed', true)
            ->assertSee('st300001')
            ->assertDontSee('st300002')
            ->call('setTab', 'transfer')
            ->assertSee('st300002')
            ->assertDontSee('st300001')
            ->call('gotoPage', 'transfer', 1)
            ->assertSee('st300002')
            // После пагинации активная вкладка не сбрасывается на «ФИО».
            ->assertSet('activeTab', 'transfer');
    }

    #[Test]
    public function buildSql_fio_mixed_layouts_produce_two_updates(): void
    {
        $sql = $this->buildSqlWithChanges([
            [
                'type' => 'fio', 'reason' => 'test',
                'login' => '100001', 'surnameOld' => 'A', 'surnameNew' => 'A',
                'nameOld' => 'B', 'nameNew' => 'B C', 'groupOld' => 'Г1', 'groupNew' => 'Г1',
                'email' => '', 'surnameInFirstname' => true,
            ],
            [
                'type' => 'fio', 'reason' => 'test',
                'login' => '100002', 'surnameOld' => 'D', 'surnameNew' => 'D',
                'nameOld' => 'E', 'nameNew' => 'E F', 'groupOld' => 'Г1', 'groupNew' => 'Г1',
                'email' => '', 'surnameInFirstname' => false,
            ],
        ]);

        $this->assertSame(2, substr_count($sql, 'UPDATE mdl_user SET'));
    }

    // ═══════════════════════════════════════════════════════════
    // P4*: process() не уничтожает результат при ошибке валидации
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function process_without_file_keeps_previous_results(): void
    {
        Cache::put('test:keep', [[
            'id' => 0, 'type' => 'expel', 'login' => '100001', 'last_name' => 'Ivanov',
            'first_name' => 'Ivan', 'middle_name' => '', 'email' => 'iv@test.ru',
            'group' => 'ИТ-24 К1', 'target_group' => '', 'comment' => '',
        ]], now()->addHours(6));

        Livewire::test(MoodleContingentSync::class)
            ->set('changesKey', 'test:keep')
            ->set('selected', [0 => true])
            ->set('stats', ['enroll' => 0, 'expel' => 1, 'fio' => 0, 'transfer' => 0, 'relink' => 0])
            ->set('parsed', true)
            ->call('process')
            // Файл не загружен → ошибка, но старый результат сохраняется.
            ->assertHasErrors('csvFile')
            ->assertSet('parsed', true)
            ->assertSet('changesKey', 'test:keep')
            ->assertSet('stats.expel', 1);

        $this->assertTrue(Cache::has('test:keep'));
    }

    // ═══════════════════════════════════════════════════════════
    // P2*: блокировка генерации SQL при недоступных данных ЛК
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function buildPreview_blocked_when_lk_unavailable(): void
    {
        Livewire::test(MoodleContingentSync::class)
            ->set('lkUnavailable', true)
            ->call('buildPreview')
            ->assertSet('sqlBuilt', false)
            ->assertSet('sqlKey', '')
            ->assertDispatched('show-toast');
    }

    #[Test]
    public function downloadSql_blocked_when_lk_unavailable(): void
    {
        Cache::put('test:block', [[
            'id' => 0, 'type' => 'expel', 'login' => '100001', 'last_name' => 'Ivanov',
            'first_name' => 'Ivan', 'middle_name' => '', 'email' => 'iv@test.ru',
            'group' => 'ИТ-24 К1', 'target_group' => '', 'comment' => '',
        ]], now()->addHours(6));

        Livewire::test(MoodleContingentSync::class)
            ->set('lkUnavailable', true)
            ->set('changesKey', 'test:block')
            ->set('selected', [0 => true])
            ->set('stats', ['enroll' => 0, 'expel' => 1, 'fio' => 0, 'transfer' => 0, 'relink' => 0])
            ->set('parsed', true)
            ->call('downloadSql')
            ->assertDispatched('show-toast')
            // Ответ-стрим не создан — ничего не скачалось.
            ->assertNoRedirect();
    }

    #[Test]
    public function process_sets_lk_unavailable_when_db_fails(): void
    {
        Cache::forget('lk:students');
        \Illuminate\Support\Facades\DB::shouldReceive('select')
            ->andThrow(new \Exception('Connection refused'));

        // Файл нужно подложить напрямую в инстанс: set() на свойство
        // TemporaryUploadedFile уничтожается Livewire-синфайлов в тестах.
        $storage = \Livewire\Features\SupportFileUploads\FileUploadConfiguration::storage();
        $storage->put(
            \Livewire\Features\SupportFileUploads\FileUploadConfiguration::path('lk-fail.csv'),
            "st_ticket,last_name,first_name,email,group_name_fixed\n"
              . "100001,Ivanov,Ivan,iv@test.ru,МГ-24 Н1\n"
        );
        $tmp = new \Livewire\Features\SupportFileUploads\TemporaryUploadedFile(
            'lk-fail.csv',
            \Livewire\Features\SupportFileUploads\FileUploadConfiguration::disk()
        );

        $component = Livewire::test(MoodleContingentSync::class);
        $component->instance()->csvFile = $tmp;
        $component->instance()->process();

        $this->assertTrue($component->instance()->parsed);
        $this->assertTrue($component->instance()->lkUnavailable);
    }

    #[Test]
    public function copySql_without_changes_shows_error(): void
    {
        Livewire::test(MoodleContingentSync::class)
            ->call('copySql')
            ->assertSet('sqlBuilt', false)
            ->assertNotDispatched('copy-sql')
            ->assertDispatched('show-toast');
    }

    #[Test]
    public function copySql_blocked_when_lk_unavailable(): void
    {
        Livewire::test(MoodleContingentSync::class)
            ->set('lkUnavailable', true)
            ->call('copySql')
            ->assertSet('sqlBuilt', false)
            ->assertNotDispatched('copy-sql')
            ->assertDispatched('show-toast');
    }

    #[Test]
    public function copySql_builds_sql_and_dispatches_it(): void
    {
        // changes — защищённое свойство, поэтому ложим в кеш: boot() восстановит
        // его по changesKey при каждой гидрации (как на реальной странице).
        Cache::put('test:copy', [[
            'type' => 'enroll', 'reason' => 'test', 'login' => '100001',
            'surnameOld' => '', 'surnameNew' => 'Ivanov',
            'nameOld' => '', 'nameNew' => 'Ivan',
            'groupOld' => '', 'groupNew' => 'МГ-24Н1 (2026)',
            'email' => 'iv@test.ru',
        ]], now()->addHours(6));

        $livewire = Livewire::test(MoodleContingentSync::class)
            ->set('changesKey', 'test:copy')
            ->set('selected', [0 => true])
            ->set('stats', ['enroll' => 1, 'expel' => 0, 'fio' => 0, 'transfer' => 0, 'relink' => 0])
            ->call('copySql')
            // SQL построен автоматически, без «Показать SQL».
            ->assertSet('sqlBuilt', true)
            ->assertDispatched('copy-sql');

        $this->assertNotSame('', $livewire->instance()->sql);
        $this->assertTrue(Cache::has($livewire->instance()->sqlKey));
    }

    // ═══════════════════════════════════════════════════════════
    // P5*: предупреждения о многогрупповых студентах ЛК
    // ═══════════════════════════════════════════════════════════

    #[Test]
    public function multiGroupWarnings_empty_when_single_group(): void
    {
        $this->assertSame([], $this->callPrivate('multiGroupWarnings', [[
            '100001' => 1, '100002' => 1,
        ]]));
    }

    #[Test]
    public function multiGroupWarnings_lists_students_in_several_groups(): void
    {
        $warnings = $this->callPrivate('multiGroupWarnings', [[
            '100001' => 1,
            '100002' => 2,
            '100003' => 3,
        ]]);

        $this->assertCount(3, $warnings);
        $this->assertStringContainsString('100002', $warnings[1]);
        $this->assertStringContainsString('100003', $warnings[2]);
    }

    #[Test]
    public function multiGroupWarnings_caps_list_at_20(): void
    {
        $logs = [];
        for ($i = 1; $i <= 25; $i++) {
            $logs["100{$i}"] = 2;
        }

        $warnings = $this->callPrivate('multiGroupWarnings', [$logs]);

        $this->assertCount(22, $warnings);
        $this->assertStringContainsString('…и ещё', end($warnings));
    }
}
