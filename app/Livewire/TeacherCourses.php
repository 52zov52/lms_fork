<?php

namespace App\Livewire;

use App\Models\CourseTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TeacherCourses extends Component
{
    public $groupId = 0;

    public function mount($groupId)
    {
        $this->groupId = $groupId;
    }

    public function render()
    {
        $teacherCourses = CourseTeacher::join('study_plan_courses_in_semesters', 'course_teacher.course_id', '=', 'study_plan_courses_in_semesters.id')
            ->join('study_plan_courses', 'study_plan_courses_in_semesters.study_plan_course_id', '=', 'study_plan_courses.id')
            ->join('study_plan_course_control_types_in_semesters', 'study_plan_courses_in_semesters.id', '=', 'study_plan_course_control_types_in_semesters.study_plan_courses_in_semester_id')
            ->join('control_types', 'study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('disciplines', 'study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('group_id', '=', $this->groupId)
            ->where('teacher_id', '=', Auth::user()->id)
            ->select(
                'course_teacher.id',
                'disciplines.name',
                'control_types.name as control_name',
                'study_plan_courses_in_semesters.lecture_hours',
                'study_plan_courses_in_semesters.practical_hours',
                'study_plan_courses_in_semesters.laboratory_hours',
                DB::raw('CASE WHEN (study_plan_courses_in_semesters.semester_number % 2 = 0) THEN 2 ELSE 1 END as half_year')
            )
            ->get();

        return view('livewire.teacher-courses', ['teacherCourses' => $teacherCourses]);
    }

    public function showEJournal($courseTeacherId)
    {
        return redirect()->route('e.journal', ['courseTeacherId' => $courseTeacherId]);
    }
}
