<?php

namespace App\Http\Controllers;

use App\Models\StudyPlan;
use Illuminate\Http\Request;

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
                'study_plan_sections.name as section_name',
                'study_plan_sections.index_name as section',
                'study_plan_indexes.index_name as index',
                'study_plan_courses.number_index',
                'study_plan_under_indexes.index_name as under_index',
                'disciplines.name as discipline_name',
                'study_plan_courses_in_semesters.semester_number',
                'study_plan_courses_in_semesters.lecture_hours',
                'study_plan_courses_in_semesters.practical_hours',
                'study_plan_courses_in_semesters.laboratory_hours',
                'study_plan_courses_in_semesters.self_study_hours'
            )
            ->orderBy('section_name')
            ->orderBy('index')
            ->orderBy('under_index')
            ->orderBy('number_index')
            ->orderBy('discipline_name')
            ->get();

        echo ('<table border=1>');
        foreach ($studyPlan as $key => $value) {
            echo ('<tr>');
            echo ('<td>' . $value->section_name . '</td>');
            echo ('<td>' . $value->section . '</td>');
            echo ('<td>' . $value->index . '</td>');
            echo ('<td>' . $value->number_index . '</td>');
            echo ('<td>' . $value->under_index . '</td>');
            echo ('<td>' . $value->discipline_name . '</td>');
            echo ('<td>' . $value->semester_number . '</td>');
            echo ('<td>' . $value->lecture_hours . '</td>');
            echo ('<td>' . $value->practical_hours . '</td>');
            echo ('<td>' . $value->laboratory_hours . '</td>');
            echo ('<td>' . $value->self_study_hours . '</td>');
            echo ('</tr>');
        }
        echo ('</table>');
    }
}
