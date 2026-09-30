<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\StudentGrades;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Cell;
use PhpOffice\PhpWord\TemplateProcessor;

class GroupStudentGradeController extends Controller
{
    public function print($groupId)
    {
        $group = Group::where('id', '=', $groupId)->select('id', 'group_name')->first();

        $studentGrades = StudentGrades::join('users_data', 'student_grades.user_id', '=', 'users_data.user_id')
            ->join('bulletins', 'student_grades.bulletin_id', '=', 'bulletins.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('bulletins.group_id', '=', $groupId)
            ->select(
                'student_grades.id',
                DB::raw("CONCAT(users_data.last_name, ' ', SUBSTRING(users_data.first_name, 1, 1), '.', SUBSTRING(users_data.middle_name, 1, 1), '.') as fio"),
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                DB::raw("CASE WHEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1) IS NOT NULL THEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1) ELSE student_grades.grade END as grade")
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->get()->toArray();

        $listOfDisciplines = StudentGrades::join('users_data', 'student_grades.user_id', '=', 'users_data.user_id')
            ->join('bulletins', 'student_grades.bulletin_id', '=', 'bulletins.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('bulletins.group_id', '=', $groupId)
            ->distinct()
            ->select(
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name'
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->orderBy('discipline_name')
            ->get()->toArray();

        $listOfStudents = array_unique(array_column($studentGrades, 'fio'));
        sort($listOfStudents);

        $groupbulletins = [];
        foreach ($listOfStudents as $key => $fio) {
            $groupbulletins[$fio] = array_filter($studentGrades, function ($item) use ($fio) {
                return $item['fio'] == $fio;
            });
        }

        $path  = Storage::disk('public')->path('template/svodka.docx');

        $filename = time() . '.docx';

        $templateProcessor = new TemplateProcessor($path);
        $templateProcessor->setValue('group_name', $group->group_name);

        $tableFontHeaderStyle = array('name' => 'Times New Roman');
        $tableFontStyle = array('name' => 'Times New Roman');
        $tableCellBtrlStyle = ['valign' => 'center', 'textDirection' => Cell::TEXT_DIR_TBRL];

        $table = new Table(array('borderSize' => 7, 'borderColor' => 'black', 'unit' => TblWidth::TWIP));
        $table->addRow(5500);
        $table->addCell(500, array('valign' => 'center'))->addText("ФИО", $tableFontHeaderStyle, array('align' => 'center'));
        foreach ($listOfDisciplines as $key => $discipline) {
            $table->addCell(500, $tableCellBtrlStyle)->addText($discipline['discipline_name'], $tableFontHeaderStyle, array('align' => 'center'));
        }

        $table->addRow();
        $table->addCell(500)->addText("", $tableFontStyle, array('align' => 'center'));
        foreach ($listOfDisciplines as $key => $discipline) {
            if($discipline['control_name'] == 'Зачет') {
                $control = 'ЗЧ';
            }else if ($discipline['control_name'] == 'Экзамен') {
                $control = 'ЭК';
            }elseif ($discipline['control_name'] == 'Дифференцированный зачет'){
                $control = 'ДЗ';
            }elseif ($discipline['control_name'] == 'Курсовая работа'){
                $control = 'КР';
            }else {
                $control = '-';
            }

            $table->addCell(500)->addText($control, $tableFontStyle, array('align' => 'center'));
        }

        foreach ($groupbulletins as $fio => $data) {
            $table->addRow();
            $table->addCell(500)->addText($fio, $tableFontStyle);
            foreach ($listOfDisciplines as $key => $discipline) {
                $existsDiscipline = array_filter($data, function ($item) use ($discipline) {
                    return $item['discipline_name'] == $discipline['discipline_name'];
                });

                if (!empty($existsDiscipline)) {
                    $grade = array_filter($existsDiscipline, function ($item) use ($discipline) {
                        return $item['discipline_name'] == $discipline['discipline_name'] &&
                            $item['semester_number'] == $discipline['semester_number'] &&
                            $item['control_name'] == $discipline['control_name'];
                    });

                    if (!empty($grade)) {
                        foreach ($grade as $key => $value) {
                            if (is_null($value['grade'])) {
                                $table->addCell(500)->addText('-', $tableFontStyle);
                            } else {
                                if ($value['control_name'] == 'Зачет') {
                                    if ($value['grade'] == 1) {
                                        $table->addCell(500)->addText(1, $tableFontStyle);
                                    } else {
                                        $table->addCell(500)->addText(0, $tableFontStyle);
                                    }
                                } else {
                                    $table->addCell(500)->addText($value['grade'], $tableFontStyle);
                                }
                            }
                        }
                    } else {
                        $table->addCell(500)->addText('-', $tableFontStyle);
                    }
                } else {
                    $table->addCell(500)->addText('-', $tableFontStyle);
                }
            }
        }

        $templateProcessor->setComplexBlock('table', $table);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $templateProcessor->saveAs($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }
}
