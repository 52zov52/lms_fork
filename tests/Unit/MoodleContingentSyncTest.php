<?php

namespace Tests\Unit;

use App\Livewire\Employee\MoodleContingentSync;
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
    private function makeStudent(string $login, string $last, string $first, string $group): array
    {
        return [
            $login => [
                'login' => $login,
                'last_name' => $last,
                'first_name' => $first,
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
        $this->component->changes = [];
        $this->component->selected = [];
        $this->component->stats = ['enroll' => 0, 'expel' => 0, 'fio' => 0, 'transfer' => 0];
        $this->callPrivate('compare', [$lk, $moodle]);
        return $this->component->changes;
    }

    /**
     * Настройка состояния и вызов buildSql().
     */
    private function buildSqlWithChanges(array $changes): string
    {
        $this->component->changes = [];
        $this->component->selected = [];
        $this->component->stats = ['enroll' => 0, 'expel' => 0, 'fio' => 0, 'transfer' => 0];

        foreach ($changes as $c) {
            $id = count($this->component->changes);
            $c['id'] = $id;
            $this->component->changes[] = $c;
            $this->component->selected[$id] = true;
            $this->component->stats[$c['type']]++;
        }

        return $this->callPrivate('buildSql');
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
        $this->assertSame('ИТ-24 К1', $result['100001']['group']);
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
}
