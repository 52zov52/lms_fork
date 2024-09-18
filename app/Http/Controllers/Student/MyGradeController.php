<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentGrades;
use Illuminate\Support\Facades\DB;

class MyGradeController extends Controller
{
    public function getMyGrades($userId)
    {
        $studentGrades = StudentGrades::join('users_data', 'student_grades.user_id', '=', 'users_data.user_id')
            ->join('bulletins', 'student_grades.bulletin_id', '=', 'bulletins.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('student_grades.user_id', '=', $userId)
            ->select(
                'student_grades.id',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                'student_grades.grade'
            )
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->get()->toArray();
        
        return $this->groupStudentGradesBySemester($studentGrades);
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
