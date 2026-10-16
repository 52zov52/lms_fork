<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\StudentExamRetakeProtocol;
use App\Models\StudentGrades;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;
use PhpOffice\PhpWord\Style\Table as TableStyle;

/**
 * Групповая сводка успеваемости.
 */
class GroupStudentGradeController extends Controller
{
    /** Сокращения видов контроля (как в старой ведомости) */
    private const CONTROL_SHORT = [
        'Экзамен' => 'ЭК',
        'Зачет' => 'ЗЧ',
        'Зачёт' => 'ЗЧ',
        'Зачет с оценкой' => 'ЗО',
        'Дифференцированный зачет' => 'ДЗ',
        'Курсовая работа' => 'КР',
        'Защита курс/р (курсов. проекта)' => 'ЗК',
        'Контрольная работа' => 'Кр',
        'Итоговая письменная к/р' => 'ИКр',
        'Экзамен по модулю' => 'ЭМ',
        'Рубежный контроль' => 'РК',
        'Защита ВКР' => 'ЗВ',
        'Госэкзамен' => 'ГЭ',
        'Комплексный экзамен' => 'КЭ',
        'Комплексный зачёт' => 'КЗ',
        'Комплексный зачет' => 'КЗ',
        'Комплексный дифзачет' => 'КДЗ',
        'Отчет по практике' => 'ЗЧ',
        'ТК по разделу' => 'ТК',
    ];

    /** Виды контроля, оценка по которым выводится как 1 / 0 */
    private const PASS_FAIL = ['Зачет', 'Зачёт', 'Комплексный зачёт', 'Комплексный зачет', 'Отчет по практике'];

    /** Виды контроля курсовых работ */
    private const COURSE_WORK = ['Курсовая работа', 'Защита курс/р (курсов. проекта)'];

    /** Максимум колонок с оценками в одной таблице */
    private const MAX_COLUMNS_PER_TABLE = 30;

    /**
     * @param int      $groupId
     * @param int|null $course
     * @return array{tempFile:string, filename:string}
     */
    public function print($groupId, $course = null)
    {
        $group = Group::select('id', 'group_name', 'course', 'year_admission', 'work_study_plan_id')->findOrFail($groupId);

        $course = (int)$course > 0 ? (int)$course : null;

        /*
         * 1. КОЛОНКИ
         */
        $planQuery = DB::table('work_study_plan_courses')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->leftJoin('indexes', 'work_study_plan_courses.index_id', '=', 'indexes.id')
            ->leftJoin('sections', 'work_study_plan_courses.section_id', '=', 'sections.id')
            ->where('work_study_plan_courses.work_study_plan_id', '=', $group->work_study_plan_id)
            ->select(
                'work_study_plan_course_control_types_in_semesters.id as cts_id',
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.id as control_id',
                'control_types.name as control_name',
                'indexes.index_name as index_name',
                'sections.index_name as section_name',
                'work_study_plan_courses.number_index'
            );

        if (!is_null($course)) {
            $planQuery->whereIn('work_study_plan_courses_in_semesters.semester_number', [$course * 2 - 1, $course * 2]);
        }

        $plan = $planQuery->get()->map(fn($item) => (array)$item)->toArray();

        $planByCourse = [];
        foreach ($plan as $row) {
            $planByCourse[(int)ceil($row['semester_number'] / 2)][] = $row;
        }
        ksort($planByCourse);

        $groupsByCourse = [];
        $ctsIds = [];
        foreach ($planByCourse as $courseNumber => $rows) {
            $groups = $this->buildColumnGroups($rows);

            if (empty($groups)) {
                continue;
            }

            $groupsByCourse[$courseNumber] = $groups;

            foreach ($groups as $g) {
                foreach ($g['columns'] as $col) {
                    foreach ($col['cts_ids'] as $id) {
                        $ctsIds[] = $id;
                    }
                }
            }
        }

        /*
         * 2. СТРОКИ
         */
        $students = GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->where('group_student.group_id', '=', $groupId)
            ->select(
                'group_student.user_id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name'
            )
            ->orderBy('users_data.last_name')
            ->orderBy('users_data.first_name')
            ->get()
            ->unique('user_id')
            ->map(function ($s) {
                $fio = $s->last_name;
                if (!empty($s->first_name)) {
                    $fio .= ' ' . mb_substr($s->first_name, 0, 1) . '.';
                }
                if (!empty($s->middle_name)) {
                    $fio .= mb_substr($s->middle_name, 0, 1) . '.';
                }
                return ['user_id' => $s->user_id, 'fio' => $fio];
            })
            ->values()
            ->toArray();

        $studentIds = array_column($students, 'user_id');

        /*
         * 3. ОЦЕНКИ
         */
        $gradeSql = "CASE
                WHEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1) IS NOT NULL
                    THEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1)
                WHEN (SELECT grade FROM student_exam_retake_protocols WHERE student_exam_retake_protocols.user_id = student_grades.user_id
                        AND student_exam_retake_protocols.work_study_plan_course_control_types_in_semesters_id = bulletins.work_study_plan_course_control_types_in_semesters_id ORDER BY id DESC LIMIT 1) IS NOT NULL
                    THEN (SELECT grade FROM student_exam_retake_protocols WHERE student_exam_retake_protocols.user_id = student_grades.user_id
                        AND student_exam_retake_protocols.work_study_plan_course_control_types_in_semesters_id = bulletins.work_study_plan_course_control_types_in_semesters_id ORDER BY id DESC LIMIT 1)
                ELSE student_grades.grade
            END as grade";

        $gradesIndex = [];

        if (!empty($studentIds) && !empty($ctsIds)) {
            $rawGrades = StudentGrades::join('bulletins', 'student_grades.bulletin_id', '=', 'bulletins.id')
                ->whereIn('student_grades.user_id', $studentIds)
                ->whereIn('bulletins.work_study_plan_course_control_types_in_semesters_id', $ctsIds)
                ->select(
                    'student_grades.user_id',
                    'bulletins.work_study_plan_course_control_types_in_semesters_id as cts_id',
                    DB::raw($gradeSql)
                )
                ->orderBy('student_grades.id')
                ->get();

            foreach ($rawGrades as $row) {
                if (is_null($row->grade)) {
                    continue;
                }

                $previous = $gradesIndex[$row->user_id][$row->cts_id] ?? null;

                if (is_null($previous) || (int)$row->grade > 0 || (int)$previous === 0) {
                    $gradesIndex[$row->user_id][$row->cts_id] = $row->grade;
                }
            }

            $rawProtocols = StudentExamRetakeProtocol::whereIn('user_id', $studentIds)
                ->whereIn('work_study_plan_course_control_types_in_semesters_id', $ctsIds)
                ->orderBy('id')
                ->get(['user_id', 'work_study_plan_course_control_types_in_semesters_id', 'grade']);

            foreach ($rawProtocols as $row) {
                if (is_null($row->grade)) {
                    continue;
                }

                $ctsId = $row->work_study_plan_course_control_types_in_semesters_id;

                if (!isset($gradesIndex[$row->user_id][$ctsId])) {
                    $gradesIndex[$row->user_id][$ctsId] = $row->grade;
                }
            }
        }

        /*
         * 4. ТЕКСТ ЯЧЕЕК
         */
        $cellText = [];
        $badCount = [];
        foreach ($groupsByCourse as $courseNumber => $groups) {
            foreach ($students as $student) {
                $uid = $student['user_id'];
                $badCount[$courseNumber][$uid] = 0;

                foreach ($groups as $g) {
                    foreach ($g['columns'] as $col) {
                        $grade = null;
                        foreach ($col['cts_ids'] as $ctsId) {
                            if (isset($gradesIndex[$uid][$ctsId])) {
                                $grade = $gradesIndex[$uid][$ctsId];
                                break;
                            }
                        }

                        if (is_null($grade)) {
                            $text = '';
                        } elseif (in_array($col['control_name'], self::PASS_FAIL, true)) {
                            $text = ((int)$grade === 1) ? '1' : '0';
                        } else {
                            $text = (string)(int)$grade;
                        }

                        $cellText[$uid][$col['key']] = $text;

                        if ($text === '' || $text === '0') {
                            $badCount[$courseNumber][$uid]++;
                        }
                    }
                }
            }
        }

        /*
         * 5. ДОКУМЕНТ
         */
        Settings::setOutputEscapingEnabled(true);

        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(8);

        $section = $phpWord->addSection([
            'orientation'  => 'landscape',
            'pageSizeW'    => 16838,
            'pageSizeH'    => 11906,
            'marginTop'    => 567,
            'marginBottom' => 567,
            'marginLeft'   => 567,
            'marginRight'  => 567,
        ]);

        if (empty($groupsByCourse)) {
            $section->addText(
                'В учебном плане группы нет дисциплин с видами контроля.',
                ['size' => 10],
                ['alignment' => Jc::CENTER]
            );
        }

        $firstCourse = true;
        foreach ($groupsByCourse as $courseNumber => $groups) {
            if (!$firstCourse) {
                $section->addPageBreak();
            }
            $firstCourse = false;

            $title = 'Сводная ведомость группы ' . trim($group->group_name) . ' за ' . $courseNumber . ' курс '
                . $this->academicYear($group, $courseNumber) . ' уч. год';
            $section->addText(
                $title,
                ['bold' => true, 'size' => 14],
                ['alignment' => Jc::CENTER, 'spaceAfter' => 200]
            );

            $blocks = $this->splitIntoBlocks($groups);
            $lastBlock = count($blocks) - 1;

            foreach ($blocks as $blockIndex => $blockGroups) {
                if ($blockIndex > 0) {
                    $section->addPageBreak();
                }

                $this->addBlockTable($section, $blockGroups, $students, $cellText, $badCount[$courseNumber], $blockIndex === $lastBlock);
            }
        }

        $groupName = trim((string) $group->group_name);
        $courses   = array_keys($groupsByCourse);

        $courseLabel = match (true) {
            $course !== null          => $course . ' курс',
            count($courses) === 1     => $courses[0] . ' курс',
            count($courses) > 1       => 'все курсы',
            default                   => 'без курса',
        };

        $filename = $this->safeFileName(
            'Сводная ведомость_' . $groupName . '_' . $courseLabel,
            'docx'
        );

        $tempFile = tempnam(sys_get_temp_dir(), 'docx');

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    /**
     * Учебный год выбранного курса: "2025/2026".
     */
    private function academicYear($group, int $course): string
    {
        $start = !empty($group->year_admission)
            ? (int)substr((string)$group->year_admission, 0, 4) + $course - 1
            : (int)date('Y') - 1;

        return $start . '/' . ($start + 1);
    }

    /**
     * Человекочитаемое имя файла вида:
     *   "Сводная ведомость - ИС-21 - 2 курс - 2025-2026 уч.год.docx"
     * Если course == null — все курсы:
     *   "Сводная ведомость - ИС-21 - все курсы - 2025-2026 уч.год.docx"
     */
    private function makeFileName($group, ?int $course, array $groupsByCourse): string
    {
        $groupName = $this->sanitizeFileName((string)$group->group_name);

        $courses = array_keys($groupsByCourse);
        if ($course !== null) {
            $courseLabel = $course . ' курс';
            $yearLabel   = $this->academicYear($group, $course);
        } elseif (!empty($courses)) {
            $courseLabel = count($courses) === 1
                ? $courses[0] . ' курс'
                : 'все курсы';
            $yearLabel = $this->academicYear($group, (int)min($courses))
                . (count($courses) > 1 ? ' и далее' : '');
        } else {
            $courseLabel = 'без курса';
            $yearLabel   = '';
        }

        $parts = [
            'Сводная ведомость',
            $groupName,
            $courseLabel,
            $yearLabel ? $yearLabel . ' уч.год' : null,
            now()->format('d.m.Y'),
        ];

        return $this->sanitizeFileName(implode(' - ', array_filter($parts))) . '.docx';
    }

    /**
     * Убирает из имени файла символы, недопустимые в Windows/Unix,
     * и схлопывает пробелы.
     */
    private function sanitizeFileName(string $name): string
    {
        $name = preg_replace('/[\\\\\/:*?"<>|\r\n\t]+/u', ' ', $name);
        $name = preg_replace('/\s+/u', ' ', $name);
        return trim($name, " .\t");
    }

    /**
     * Из строк учебного плана строит группы колонок.
     *
     * @return array<int, array{title:string, sort:string, semester:int, columns:array}>
     */
    private function buildColumnGroups(array $plan): array
    {
        $groups = [];

        foreach ($plan as $row) {
            $controlName = trim((string)$row['control_name']);

            if ($controlName === '' || $controlName === '-') {
                continue;
            }

            $name = trim(preg_replace('/\s+/u', ' ', (string)$row['discipline_name']));
            $isCourseWork = in_array($controlName, self::COURSE_WORK, true);

            $displayName = $isCourseWork
                ? 'Курсовая работа по ' . $this->courseWorkPrefix($row) . $name
                : $name;

            $prefix = '';
            if (!is_null($row['index_name'])) {
                $prefix = $row['index_name'] . (!is_null($row['section_name']) ? '.' . $row['section_name'] : '');
            }

            $title = ($prefix !== '' ? $prefix . ' - ' : '') . $displayName;

            $groupKey = $row['semester_number'] . '|' . $prefix . '|' . mb_strtolower($displayName);
            $columnKey = $groupKey . '|' . $controlName;

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'title' => $title,
                    'sort' => mb_strtolower($displayName),
                    'semester' => (int)$row['semester_number'],
                    'columns' => [],
                ];
            }

            if (!isset($groups[$groupKey]['columns'][$columnKey])) {
                $groups[$groupKey]['columns'][$columnKey] = [
                    'key' => $columnKey,
                    'control_id' => (int)$row['control_id'],
                    'control_name' => $controlName,
                    'short' => self::CONTROL_SHORT[$controlName] ?? mb_substr($controlName, 0, 3),
                    'cts_ids' => [],
                ];
            }

            $groups[$groupKey]['columns'][$columnKey]['cts_ids'][] = (int)$row['cts_id'];
        }

        foreach ($groups as &$g) {
            $g['columns'] = array_values($g['columns']);
            usort($g['columns'], fn($a, $b) => $a['control_id'] <=> $b['control_id']);
        }
        unset($g);

        $groups = array_values($groups);
        usort($groups, fn($a, $b) => [$a['semester'], $a['sort']] <=> [$b['semester'], $b['sort']]);

        return $groups;
    }

    /**
     * "ОП.06 " для общепрофессиональных дисциплин.
     */
    private function courseWorkPrefix(array $row): string
    {
        if (($row['index_name'] ?? null) === 'ОПД' && !empty($row['number_index'])) {
            return 'ОП.' . sprintf('%02d', (int)$row['number_index']) . ' ';
        }

        return '';
    }

    /**
     * Делит группы колонок на блоки не более MAX_COLUMNS_PER_TABLE колонок.
     */
    private function splitIntoBlocks(array $groups): array
    {
        $blocks = [];
        $current = [];
        $count = 0;

        foreach ($groups as $g) {
            $size = count($g['columns']);

            if ($count > 0 && $count + $size > self::MAX_COLUMNS_PER_TABLE) {
                $blocks[] = $current;
                $current = [];
                $count = 0;
            }

            $current[] = $g;
            $count += $size;
        }

        if (!empty($current)) {
            $blocks[] = $current;
        }

        return $blocks;
    }

    private function addBlockTable($section, array $blockGroups, array $students, array $cellText, array $badCount, bool $withTotals): void
    {
        $usableWidth = 15300;
        $numWidth = 420;
        $fioWidth = 2000;
        $totalWidth = $withTotals ? 650 : 0;

        $columnCount = 0;
        foreach ($blockGroups as $g) {
            $columnCount += count($g['columns']);
        }

        $colWidth = (int)floor(($usableWidth - $numWidth - $fioWidth - $totalWidth) / max(1, $columnCount));
        $colWidth = max(300, min(700, $colWidth));

        $center = ['alignment' => Jc::CENTER, 'spaceBefore' => 0, 'spaceAfter' => 0, 'lineHeight' => 1];
        $left = ['alignment' => Jc::LEFT, 'spaceBefore' => 0, 'spaceAfter' => 0, 'lineHeight' => 1];
        $headFont = ['name' => 'Times New Roman', 'size' => 7];
        $font = ['name' => 'Times New Roman', 'size' => 8];

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 30,
            'layout' => TableStyle::LAYOUT_FIXED,
            'unit' => TblWidth::TWIP,
        ]);

        // Строка 1
        $table->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        $table->addCell($numWidth, ['vMerge' => 'restart', 'valign' => 'center'])->addText('№', $font, $center);
        $table->addCell($fioWidth, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Фамилия И.О.', $font, $center);
        $table->addCell($colWidth * $columnCount, ['gridSpan' => $columnCount, 'valign' => 'center'])
            ->addText('Наименование дисциплины (модуля), практики', $font, $center);
        if ($withTotals) {
            $table->addCell($totalWidth, [
                'vMerge' => 'restart',
                'valign' => 'center',
                'textDirection' => Cell::TEXT_DIR_BTLR,
            ])->addText('Количество нулей и пробелов', $headFont, $center);
        }

        // Строка 2: названия дисциплин
        $table->addRow(5200, ['tblHeader' => true, 'cantSplit' => true, 'exactHeight' => true]);
        $table->addCell($numWidth, ['vMerge' => 'continue']);
        $table->addCell($fioWidth, ['vMerge' => 'continue']);
        foreach ($blockGroups as $g) {
            $span = count($g['columns']);
            $table->addCell($colWidth * $span, [
                'gridSpan' => $span,
                'valign' => 'center',
                'textDirection' => Cell::TEXT_DIR_BTLR,
            ])->addText($g['title'], $headFont, $left);
        }
        if ($withTotals) {
            $table->addCell($totalWidth, ['vMerge' => 'continue']);
        }

        // Строка 3: виды контроля
        $table->addRow(null, ['tblHeader' => true, 'cantSplit' => true]);
        $table->addCell($numWidth, ['vMerge' => 'continue']);
        $table->addCell($fioWidth, ['vMerge' => 'continue']);
        foreach ($blockGroups as $g) {
            foreach ($g['columns'] as $col) {
                $table->addCell($colWidth, ['valign' => 'center'])->addText($col['short'], $headFont, $center);
            }
        }
        if ($withTotals) {
            $table->addCell($totalWidth, ['vMerge' => 'continue']);
        }

        // Студенты
        foreach ($students as $i => $student) {
            $uid = $student['user_id'];

            $table->addRow(null, ['cantSplit' => true]);
            $table->addCell($numWidth, ['valign' => 'center'])->addText((string)($i + 1), $font, $center);
            $table->addCell($fioWidth, ['valign' => 'center'])->addText((string)$student['fio'], $font, $left);

            foreach ($blockGroups as $g) {
                foreach ($g['columns'] as $col) {
                    $table->addCell($colWidth, ['valign' => 'center'])
                        ->addText($cellText[$uid][$col['key']] ?? '', $font, $center);
                }
            }

            if ($withTotals) {
                $table->addCell($totalWidth, ['valign' => 'center'])->addText((string)($badCount[$uid] ?? 0), $font, $center);
            }
        }
    }

    private function safeFileName(string $name, string $ext): string
    {
        $name = preg_replace('/[\\\\\/:*?"<>|\r\n\t]+/u', ' ', $name);
        $name = preg_replace('/\s+/u', ' ', $name);

        return trim($name, " .\t") . '.' . $ext;
    }
}