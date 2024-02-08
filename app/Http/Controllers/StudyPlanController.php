<?php

namespace App\Http\Controllers;

use App\Models\StudyPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\Empty_;

class StudyPlanController extends Controller
{
    public function getStudyPlan()
    {
        $studyPlan = StudyPlan::where('study_plans.id', 520)
            ->join('study_plan_courses', 'study_plans.id', '=', 'study_plan_courses.study_plan_id')
            ->join('disciplines', 'study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->join('study_plan_indexes', 'study_plan_courses.study_plan_index_id', '=', 'study_plan_indexes.id')
            ->join('study_plan_under_indexes', 'study_plan_courses.study_plan_under_index_id', '=', 'study_plan_under_indexes.id')
            ->join('study_plan_sections', 'study_plan_courses.study_plan_section_id', '=', 'study_plan_sections.id')
            ->join('study_plan_courses_in_semesters', 'study_plan_courses.id', '=', 'study_plan_courses_in_semesters.study_plan_course_id')
            ->select(
                'study_plan_courses.id',
                'study_plan_sections.name as section_name',
                'study_plan_sections.index_name as section',
                'study_plan_indexes.name as full_name_index',
                'study_plan_indexes.index_name as index',
                'study_plan_courses.number_index',
                'study_plan_under_indexes.name as full_name_under_index',
                'study_plan_under_indexes.index_name as under_index',
                'disciplines.name as discipline_name',
                DB::raw('SUM(study_plan_courses_in_semesters.lecture_hours + study_plan_courses_in_semesters.practical_hours + study_plan_courses_in_semesters.laboratory_hours + study_plan_courses_in_semesters.individual_consultation_hours + study_plan_courses_in_semesters.group_consultation_hours + study_plan_courses_in_semesters.control_hours + study_plan_courses_in_semesters.self_study_hours) as sum_1'),
                DB::raw('SUM(study_plan_courses_in_semesters.lecture_hours + study_plan_courses_in_semesters.practical_hours + study_plan_courses_in_semesters.laboratory_hours + study_plan_courses_in_semesters.individual_consultation_hours + study_plan_courses_in_semesters.group_consultation_hours + study_plan_courses_in_semesters.control_hours) as sum_2'),
                DB::raw('SUM(study_plan_courses_in_semesters.lecture_hours) as lecture_hours'),
                DB::raw('SUM(study_plan_courses_in_semesters.practical_hours) as practical_hours'),
                DB::raw('SUM(study_plan_courses_in_semesters.laboratory_hours) as laboratory_hours'),
                DB::raw('SUM(study_plan_courses_in_semesters.individual_consultation_hours) as individual_consultation_hours'),
                DB::raw('SUM(study_plan_courses_in_semesters.group_consultation_hours) as group_consultation_hours'),
                DB::raw('SUM(study_plan_courses_in_semesters.control_hours) as control_hours'),
                DB::raw('SUM(study_plan_courses_in_semesters.self_study_hours) as self_study_hours'),
                DB::raw('SUM(study_plan_courses_in_semesters.work_content_first) as work_content_first')
            )
            ->groupBy('study_plan_courses.id')
            ->orderBy('study_plan_indexes.sorting_order')
            ->orderBy('study_plan_sections.sorting_order')
            ->orderBy('study_plan_under_indexes.sorting_order')
            ->orderBy('number_index')
            ->get()->toArray();

        $result = $this->getFormControlsInSemesters($studyPlan);

        $a1 = "";
        $a2 = "";
        $a3 = "";

        echo ('<table border=1>');

        echo ('<tr>');
        echo ('<td>Индекс</td>');
        echo ('<td>Дис.</td>');
        echo ('<td>Э</td>');
        echo ('<td>ДЗ</td>');
        echo ('<td>Зачет</td>');
        echo ('<td>КР</td>');
        echo ('<td>Всего</td>');
        echo ('<td>Итого</td>');
        echo ('<td>Лекции</td>');
        echo ('<td>Практики</td>');
        echo ('<td>Лаб</td>');
        echo ('<td>внеауд</td>');
        echo ('<td>ауд</td>');
        echo ('<td>контроль</td>');
        echo ('<td>сам контроль</td>');
        echo ('<td>З.Е.</td>');
        echo ('<td>1сем</td>');
        echo ('<td>2сем</td>');
        echo ('<td>3сем</td>');
        echo ('<td>4сем</td>');
        echo ('<td>5сем</td>');
        echo ('<td>6сем</td>');
        echo ('<td>7сем</td>');
        echo ('<td>8сем</td>');
        echo ('<td>9сем</td>');
        echo ('<td>10сем</td>');
        echo ('<td>Форма ГИА/ПА</td>');
        echo ('</tr>');
        foreach ($result as $key => $value) {
            echo ('<tr>');
            echo ('<td>' . $value['index'] . '.' . $value['section'] . '.' . $value['number_index'] . '</td>');
            echo ('<td>' . $value['discipline_name'] . '</td>');

            echo ('<td>' . $value['exam'] . '</td>');
            echo ('<td>' . $value['differentiated_zachet'] . '</td>');
            echo ('<td>' . $value['zachet'] . '</td>');
            echo ('<td>' . $value['course_work'] . '</td>');

            echo ('<td>' . $value['sum_1'] . '</td>');
            echo ('<td>' . $value['sum_2'] . '</td>');

            echo ('<td>' . $value['lecture_hours'] . '</td>');
            echo ('<td>' . $value['practical_hours'] . '</td>');
            echo ('<td>' . $value['laboratory_hours'] . '</td>');
            echo ('<td>' . $value['individual_consultation_hours'] . '</td>');
            echo ('<td>' . $value['group_consultation_hours'] . '</td>');
            echo ('<td>' . $value['control_hours'] . '</td>');
            echo ('<td>' . $value['self_study_hours'] . '</td>');
            echo ('<td>' . $value['work_content_first'] . '</td>');

            if (array_key_exists(1, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][1] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(2, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][2] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(3, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][3] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(4, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][4] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(5, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][5] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(6, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][6] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(7, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][7] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(8, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][8] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(9, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][9] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            if (array_key_exists(10, $value['workload_in_semesters'])) {
                echo ('<td>' . $value['workload_in_semesters'][10] . '</td>');
            } else {
                echo ('<td> - </td>');
            }

            echo ('<td>' . $value['form_controls'] . '</td>');

            echo ('</tr>');
        }
        echo ('</table>');
    }

    public function getFormControlsInSemesters($data)
    {
        $tmp = array();

        $exam = "";
        $zachet = "";
        $differentiatedZachet = "";
        $courseWork = "";

        $formControls = "";

        foreach ($data as $key => $value) {
            $formControlsInSemesters = DB::table('study_plan_courses_in_semesters')
                ->join('study_plan_course_control_types_in_semesters', 'study_plan_courses_in_semesters.id', '=', 'study_plan_course_control_types_in_semesters.study_plan_courses_in_semester_id')
                ->join('control_types', 'study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
                ->where('study_plan_courses_in_semesters.study_plan_course_id', '=', $value['id'])
                ->select('study_plan_courses_in_semesters.semester_number', 'study_plan_courses_in_semesters.work_content_first', 'control_types.name')
                ->get()->toArray();

            $sem = array();

            for ($i = 0; $i < count($formControlsInSemesters); $i++) {
                switch (trim($formControlsInSemesters[$i]->name)) {
                    case 'Экзамен':
                        $exam .= $formControlsInSemesters[$i]->semester_number . ', ';
                        $formControls .= 'Э' . ', ';
                        break;
                    case 'Зачет':
                        $zachet .= $formControlsInSemesters[$i]->semester_number . ', ';
                        $formControls .= 'Зач' . ', ';
                        break;
                    case 'Дифференцированный зачет':
                        $differentiatedZachet .= $formControlsInSemesters[$i]->semester_number . ', ';
                        $formControls .= 'ДЗ' . ', ';
                        break;
                    case 'Курсовая работа':
                        $courseWork .= $formControlsInSemesters[$i]->semester_number . ', ';
                        $formControls .= 'К/Р' . ', ';
                        break;
                    default:
                        # code...
                        break;
                }

                switch ($formControlsInSemesters[$i]->semester_number) {
                    case 1:
                        $sem[1] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 2:
                        $sem[2] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 3:
                        $sem[3] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 4:
                        $sem[4] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 5:
                        $sem[5] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 6:
                        $sem[6] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 7:
                        $sem[7] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 8:
                        $sem[8] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 9:
                        $sem[9] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    case 10:
                        $sem[10] = $formControlsInSemesters[$i]->work_content_first;
                        break;
                    default:
                        # code...
                        break;
                }
            }

            $value['exam'] = substr($exam, 0, -2);
            $value['zachet'] = substr($zachet, 0, -2);
            $value['differentiated_zachet'] =  substr($differentiatedZachet, 0, -2);
            $value['course_work'] = substr($courseWork, 0, -2);
            $value['workload_in_semesters'] = $sem;
            $value['form_controls'] = substr($formControls, 0, -2);

            $tmp[] = $value;

            $exam = "";
            $zachet = "";
            $differentiatedZachet = "";
            $courseWork = "";

            unset($sem);

            $formControls = "";
        }

        return $tmp;
    }
}
