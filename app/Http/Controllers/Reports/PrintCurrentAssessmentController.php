<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\CourseTeacher;
use App\Models\EJournal;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;

class PrintCurrentAssessmentController extends Controller
{
    public function printCurrentAssessment($courseTeacherId)
    {
        $infoAboutGroupAndCourse = CourseTeacher::where('course_teacher.id', '=', $courseTeacherId)
            ->join('work_study_plan_courses_in_semesters', 'course_teacher.course_id', '=', 'work_study_plan_courses_in_semesters.external_id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
            ->join('specialities', 'work_study_plans.speciality_id', '=', 'specialities.id')
            ->join('groups', 'course_teacher.group_id', '=', 'groups.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'work_study_plan_courses_in_semesters.id', '=', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->select(
                'course_teacher.academic_year',
                'work_study_plan_courses_in_semesters.semester_number',
                'teaching_forms.teaching_form_name',
                'specialities.speciality_name',
                'specialities.specialty_code',
                'groups.group_name',
                'disciplines.name as discipline_name',
                'control_types.name as control_name'
            )
            ->first();

        $data = $this->dataTransform(EJournal::join('users_data', 'e_journals.student_id', '=', 'users_data.user_id')
            ->whereIn('e_journals.lesson_type', [4, 5, 6, 7])
            ->where('e_journals.course_teacher_id', '=', $courseTeacherId)
            ->select(
                'e_journals.id',
                DB::raw("CONCAT(users_data.last_name,' ', users_data.first_name, ' ', users_data.middle_name) AS fio"),
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = e_journals.student_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                'e_journals.status',
                'e_journals.lesson_type'
            )
            ->get()->toArray());

        $filename = time() . '.pdf';
        $pdf = PDF::loadView('reports.pdf.current-assessment', ['infoAboutGroupAndCourse' => $infoAboutGroupAndCourse, 'data' => $data])->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    private function dataTransform($data)
    {
        $transformedData = array();

        $ticketNumber = null;
        $lessonTypeTest = null;
        $lessonTypeExam = null;

        $sum = 0;

        $fio = array_unique(array_column($data, 'fio'));

        foreach ($fio as $user) {
            $result = array_filter($data, function($item) use ($user){
                return $item['fio'] == $user;
            });

            $lessonTypeTK = [];

            foreach ($result as $key => $value) {
                $ticketNumber =$value['ticket_number'];

                if($value['lesson_type'] == 4) {
                    $lessonTypeTK[] = $value['status'];

                    if($value['status'] == 4) {
                        $sum += 0;
                    }

                    if($value['status'] == 5) {
                        $sum += 1;
                    }

                    if($value['status'] == 6) {
                        $sum += 2;
                    }

                    if($value['status'] == 7) {
                        $sum += 3;
                    }

                    if($value['status'] == 8) {
                        $sum += 4;
                    }

                    if($value['status'] == 9) {
                        $sum += 5;
                    }
                }

                if($value['lesson_type'] == 5) {
                    $lessonTypeTest = $value['status'];

                    if($value['status'] == 4) {
                        $sum += 0;
                    }

                    if($value['status'] == 5) {
                        $sum += 1;
                    }

                    if($value['status'] == 6) {
                        $sum += 2;
                    }

                    if($value['status'] == 7) {
                        $sum += 3;
                    }

                    if($value['status'] == 8) {
                        $sum += 4;
                    }

                    if($value['status'] == 9) {
                        $sum += 5;
                    }
                }

                if($value['lesson_type'] == 6 || $value['lesson_type'] == 7 || $value['lesson_type'] == 8) {
                    $lessonTypeExam = $value['status'];
                }
            }

            $transformedData[] = [
                'fio' => $user,
                'ticket_number' => $ticketNumber,
                'lesson_type_tk' => $lessonTypeTK,
                'lesson_type_test' => $lessonTypeTest,
                'result_tk' => round($sum / 4, 0),
                'lesson_type_exam' => $lessonTypeExam,
            ];

            unset($lessonTypeTK);
            $sum = 0;
        }

        return $transformedData;
    }
}
