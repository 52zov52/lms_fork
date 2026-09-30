<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\StudentGrades;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;
use PhpOffice\PhpWord\TemplateProcessor;

class GroupStudentGradeController extends Controller
{
    public function print($groupId)
    {
        $group = Group::select('id', 'group_name', 'work_study_plan_id')->findOrFail($groupId);

        /*
         * 1. КОЛОНКИ: все дисциплины учебного плана группы
         *    (а не только те, по которым уже есть ведомости).
         */
        $listOfDisciplines = DB::table('work_study_plan_courses')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('work_study_plan_courses.work_study_plan_id', '=', $group->work_study_plan_id)
            // Если нужны только пройденные семестры, раскомментируйте и задайте номер:
            // ->where('work_study_plan_courses_in_semesters.semester_number', '<=', $currentSemester)
            ->distinct()
            ->select(
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name'
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->orderBy('disciplines.name')
            ->get()
            ->map(fn($item) => (array)$item)
            ->toArray();

        /*
         * 2. СТРОКИ: все студенты группы (даже без единой оценки).
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

        /*
         * 3. ОЦЕНКИ: индекс [user_id][дисциплина|семестр|контроль] => оценка.
         *    Учитывается последняя пересдача.
         */
        $rawGrades = StudentGrades::join('bulletins', 'student_grades.bulletin_id', '=', 'bulletins.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('bulletins.group_id', '=', $groupId)
            ->select(
                'student_grades.user_id',
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                DB::raw("COALESCE((SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1), student_grades.grade) as grade")
            )
            ->get();

        $gradesIndex = [];
        foreach ($rawGrades as $row) {
            $key = $row->discipline_name . '|' . $row->semester_number . '|' . $row->control_name;
            $gradesIndex[$row->user_id][$key] = $row->grade;
        }

        /*
         * 4. ШАБЛОН И ТАБЛИЦА
         */
        $path = Storage::disk('public')->path('template/svodka.docx');
        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('group_name', $group->group_name);

        $tableFontHeaderStyle = ['name' => 'Times New Roman'];
        $tableFontStyle = ['name' => 'Times New Roman'];
        $tableCellBtrlStyle = ['valign' => 'center', 'textDirection' => Cell::TEXT_DIR_TBRL];

        $controlShort = [
            'Зачет' => 'ЗЧ',
            'Экзамен' => 'ЭК',
            'Дифференцированный зачет' => 'ДЗ',
            'Курсовая работа' => 'КР',
        ];

        // Экранирование &, <, > и т.п. в названиях (иначе docx "повреждён")
        Settings::setOutputEscapingEnabled(true);

        $table = new Table(['borderSize' => 7, 'borderColor' => 'black', 'unit' => TblWidth::TWIP]);

        // В Word в таблице не может быть больше 63 колонок, поэтому режем
        // дисциплины на блоки и выводим блоки друг под другом в одной таблице.
        $chunkSize = 25;
        $blocks = array_chunk($listOfDisciplines, $chunkSize);
        if (empty($blocks)) {
            $blocks = [[]];
        }

        foreach ($blocks as $blockIndex => $block) {
            // Пустая строка-разделитель между блоками
            if ($blockIndex > 0) {
                $table->addRow(300);
                $table->addCell(500)->addText('', $tableFontStyle);
            }

            // Строка 1: заголовки (ФИО + повёрнутые названия дисциплин)
            $table->addRow(5500);
            $table->addCell(500, ['valign' => 'center'])->addText('ФИО', $tableFontHeaderStyle, ['align' => 'center']);
            foreach ($block as $discipline) {
                $table->addCell(500, $tableCellBtrlStyle)->addText((string)$discipline['discipline_name'], $tableFontHeaderStyle, ['align' => 'center']);
            }

            // Строка 2: виды контроля
            $table->addRow();
            $table->addCell(500)->addText('', $tableFontStyle, ['align' => 'center']);
            foreach ($block as $discipline) {
                $control = $controlShort[$discipline['control_name']] ?? '-';
                $table->addCell(500)->addText($control, $tableFontStyle, ['align' => 'center']);
            }

            // Строки студентов: ровно одна ячейка на каждую колонку
            foreach ($students as $student) {
                $table->addRow();
                $table->addCell(500)->addText((string)$student['fio'], $tableFontStyle);

                foreach ($block as $discipline) {
                    $key = $discipline['discipline_name'] . '|' . $discipline['semester_number'] . '|' . $discipline['control_name'];
                    $grade = $gradesIndex[$student['user_id']][$key] ?? null;

                    if (is_null($grade)) {
                        $text = '-';
                    } elseif ($discipline['control_name'] == 'Зачет') {
                        $text = ($grade == 1) ? '1' : '0';
                    } else {
                        $text = (string)$grade;
                    }

                    $table->addCell(500)->addText($text, $tableFontStyle);
                }
            }
        }

        $templateProcessor->setComplexBlock('table', $table);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $templateProcessor->saveAs($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
