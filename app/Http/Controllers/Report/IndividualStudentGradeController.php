<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\GroupStudent;
use App\Models\StudentGrades;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\DB;

class IndividualStudentGradeController extends Controller
{
    public function print($userId)
    {
        $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
            ->where('user_id', '=', $userId)
            ->select(
                'group_student.id',
                'group_student.group_id',
                'groups.work_study_plan_id'
            )
            ->orderBy('id', 'desc')
            ->first();

        $studentGrades = StudentGrades::join('users_data', 'student_grades.user_id', '=', 'users_data.user_id')
            ->join('bulletins', 'student_grades.bulletin_id', '=', 'bulletins.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plans', 'work_study_plan_courses.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('student_grades.user_id', '=', $userId)
            ->where('work_study_plans.id', '=', $groupStudent->work_study_plan_id)
            ->select(
                'student_grades.id',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                DB::raw("CASE WHEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1) IS NOT NULL THEN (SELECT grade FROM student_retake_bulletins WHERE student_grades_id = student_grades.id ORDER BY id DESC LIMIT 1) ELSE student_grades.grade END as grade")
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->get()->toArray();

        $filename = time() . '.pdf';

        $pdf = PDF::loadView('reports.pdf.individual-student-grades', [
            'fio' => $studentGrades[0]['fio'],
            'studentGrades' => $this->groupStudentGradesBySemester($studentGrades)
        ]);

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $pdf->save($tempFile);

        return ['tempFile' => $tempFile, 'filename' => $filename];
    }

    private function groupStudentGradesBySemester($studentGrades): array
    {
        $results = [];
        $semesters = array_unique(array_column($studentGrades, 'semester_number'));

        foreach ($semesters as $key => $value) {
            $results[$value] = array_filter($studentGrades, function ($item) use ($value) {
                return $item['semester_number'] == $value;
            });
        }

        return $results;
    }
}
