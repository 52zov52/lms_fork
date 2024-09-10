<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\WorkStudyPlanCourse;

class MyDisciplineController extends Controller
{
    public function getDisciplinesForStudentInYear($userId): array
    {
        $group = Group::select('course', 'work_study_plan_id')
            ->where('id', '=', function ($query) use ($userId) {
                $query->select('group_id')
                    ->from('group_student')
                    ->where('user_id', '=', $userId)
                    ->orderBy('id', 'desc')
                    ->limit(1);
            })->first();

        $semesters = $this->getCurentSemesters($group->course);

        $workStudyPlanCourse = WorkStudyPlanCourse::select('disciplines.name', 'work_study_plan_courses_in_semesters.semester_number')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_courses.id', '=', 'work_study_plan_courses_in_semesters.work_study_plan_course_id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('work_study_plan_courses.work_study_plan_id', '=', $group->work_study_plan_id)
            ->whereIn('work_study_plan_courses_in_semesters.semester_number', $semesters)
            ->orderBy('work_study_plan_courses_in_semesters.semester_number')
            ->orderBy('disciplines.name')
            ->distinct()
            ->get()->toArray();

        return $this->groupWorkStudyPlanCoursesBySemester($workStudyPlanCourse, $semesters);
    }

    private function getCurentSemesters($course): array
    {
        switch ($course) {
            case 1:
                return [1, 2];
                break;
            case 2:
                return [3, 4];
                break;
            case 3:
                return [5, 6];
                break;
            case 4:
                return [7, 8];
                break;
            case 5:
                return [9, 10];
                break;
            default:
                break;
        }
    }

    private function groupWorkStudyPlanCoursesBySemester($workStudyPlanCourse, $semesters): array
    {
        $results = [];
        foreach ($semesters as $key => $value) {
            $results[$value] = array_filter($workStudyPlanCourse, function ($item) use ($value) {
                return $item['semester_number'] == $value;
            });
        }

        return $results;
    }
}
