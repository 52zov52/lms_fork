<?php

namespace App\Support;

/**
 * Разбор приказов студента для справок: академический отпуск, выход из него,
 * отчисление (по завершении обучения / без завершения), восстановление.
 *
 * Как устроены данные (order_user.notes = состояние студента ДО приказа):
 *  - «Незачисленные»  -> приказ о зачислении;
 *  - «Отчисленные»    -> приказ о восстановлении (а предыдущий индивидуальный приказ с группой в notes = отчисление);
 *  - «Академотпуск»   -> приказ о выходе из академического отпуска (а предыдущий индивидуальный приказ с группой = предоставление отпуска);
 *  - название группы  -> может быть отпуск, отчисление или перевод: тип определяется по соседним приказам,
 *                        по таблице students_academic_leaves и по последнему состоянию студента.
 * Индивидуальные приказы имеют номер вида «123/с» (иногда с латинской «c», с хвостом «от 01.09.2026»);
 * массовые («распределение», «деление на группы», «переход на новый стандарт» и т.п.) в расчёт не берутся.
 */
class StudentOrderHistory
{
    public const NOTE_LEAVE = 'Академотпуск';
    public const NOTE_EXPELLED = 'Отчисленные';
    public const NOTE_NOT_ENROLLED = 'Незачисленные';

    private const STATE_EXPELLED = 2;
    private const STATE_GRADUATED = 3;

    private const LABELS = [
        'leave' => 'Приказ о предоставлении академического отпуска',
        'return' => 'Приказ о выходе из академического отпуска',
        'expel' => 'Приказ об отчислении в связи с незавершением обучения образовательной программы',
        'graduate' => 'Приказ об отчислении в связи с успешным завершением обучения',
        'restore' => 'Приказ о восстановлении',
    ];

    /**
     * @param array $orderRows  приказы студента по order_user, по возрастанию даты:
     *                          [id, order_id, order_number, order_date, notes]
     * @param array $leaveRows  приказы из students_academic_leaves: [order_id, order_number, order_date]
     * @param int|null $lastStateId последнее состояние студента (student_state.state_id): 1 отпуск, 2 отчисление, 3 выпуск
     * @param bool $inGroup     есть ли студент сейчас в group_student
     * @param string|null $completionDate дата окончания обучения группы (Y-m-d). Отчисление в эту дату
     *                          или позже считается отчислением в связи с завершением обучения.
     *
     * @return array{hadLeave: bool, lines: string[]}
     *         hadLeave = true, если есть хотя бы один приказ (отпуск, выход, отчисление, восстановление)
     */
    public static function build(
        array $orderRows,
        array $leaveRows,
        ?int $lastStateId,
        bool $inGroup,
        ?string $completionDate = null
    ): array {
        $events = [];
        $candidate = null; // индекс последнего индивидуального приказа с группой в notes

        foreach ($orderRows as $row) {
            $number = trim((string) $row['order_number']);
            $date = substr((string) $row['order_date'], 0, 10);
            $note = trim((string) $row['notes']);

            // служебные записи («-1», дата 1900-01-01)
            if ($date < '1990-01-01' || str_starts_with($number, '-')) {
                continue;
            }

            if ($note === self::NOTE_EXPELLED) {
                if ($candidate !== null && $events[$candidate]['kind'] === null) {
                    $events[$candidate]['kind'] = 'expel';
                }
                $candidate = null;
                $events[] = self::event('restore', $row, $number, $date);
            } elseif ($note === self::NOTE_LEAVE) {
                if ($candidate !== null && $events[$candidate]['kind'] === null) {
                    $events[$candidate]['kind'] = 'leave';
                }
                $candidate = null;
                $events[] = self::event('return', $row, $number, $date);
            } elseif ($note === '' || $note === self::NOTE_NOT_ENROLLED) {
                continue;
            } elseif (self::isPersonnelNumber($number)) {
                $events[] = self::event(null, $row, $number, $date);
                $candidate = array_key_last($events);
            }
        }

        // Приказы об академическом отпуске из специальной таблицы
        foreach ($leaveRows as $leave) {
            $found = null;
            foreach ($events as $i => $event) {
                if ((int) $event['order_id'] === (int) $leave['order_id']) {
                    $found = $i;
                    break;
                }
            }

            if ($found === null) {
                $events[] = self::event('leave', $leave, trim((string) $leave['order_number']), substr((string) $leave['order_date'], 0, 10));
            } elseif ($events[$found]['kind'] === null) {
                $events[$found]['kind'] = 'leave';
            }
        }

        // Последний индивидуальный приказ с группой, который так и остался без типа:
        //  - студент выпущен (state 3)  -> отчисление в связи с завершением обучения;
        //  - студент отчислен (state 2) -> отчисление в связи с незавершением обучения.
        // Если студента восстанавливали, а в группе он числится, состояние «отчисление» могло устареть — не доверяем ему.
        $hasRestore = (bool) array_filter($events, fn($e) => $e['kind'] === 'restore');
        if ($candidate !== null && $events[$candidate]['kind'] === null) {
            if ($lastStateId === self::STATE_GRADUATED) {
                $events[$candidate]['kind'] = 'graduate';
            } elseif ($lastStateId === self::STATE_EXPELLED && (!$hasRestore || !$inGroup)) {
                $events[$candidate]['kind'] = 'expel';
            }
        }

        $events = array_values(array_filter($events, fn($e) => $e['kind'] !== null));

        // Отчисление в день окончания срока обучения или позже — это завершение обучения
        if ($completionDate !== null) {
            $events = array_map(function ($e) use ($completionDate) {
                if ($e['kind'] === 'expel' && $e['date'] >= $completionDate) {
                    $e['kind'] = 'graduate';
                }
                return $e;
            }, $events);
        }

        usort($events, fn($a, $b) => [$a['date'], $a['order_id']] <=> [$b['date'], $b['order_id']]);

        // Галочка ставится, если есть хоть один приказ: отпуск, выход, отчисление, восстановление
        $hadLeave = count($events) > 0;

        $lines = array_map(
            fn($e) => self::LABELS[$e['kind']] . ' от ' . self::formatDate($e['date']) . ' № ' . self::cleanNumber($e['number']) . ';',
            $events
        );

        return ['hadLeave' => $hadLeave, 'lines' => $lines];
    }

    /** «438/с от 31.08.2026» -> «438/с»; латинскую «c» после слэша заменяем на русскую «с» */
    public static function cleanNumber(string $number): string
    {
        $number = trim($number);
        $number = preg_replace('/\s+от\s+\d{2}\.\d{2}\.\d{4}.*$/u', '', $number);
        $number = preg_replace('/\/\s*[cCС](?![\p{L}\p{N}])/u', '/с', $number);

        return trim($number);
    }

    /** Индивидуальный приказ: «123/с», «123/c», «123/с от 01.09.2026» */
    private static function isPersonnelNumber(string $number): bool
    {
        return (bool) preg_match('/^\s*\d+\s*\/\s*[сcСC](?![\p{L}\p{N}])/u', $number);
    }

    private static function formatDate(string $date): string
    {
        return date('d.m.Y', strtotime($date));
    }

    private static function event(?string $kind, array $row, string $number, string $date): array
    {
        return [
            'kind' => $kind,
            'order_id' => $row['order_id'],
            'number' => $number,
            'date' => $date,
        ];
    }
}