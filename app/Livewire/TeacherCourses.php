<?php

namespace App\Livewire;

use App\Models\CourseTeacher;
use Illuminate\Support\Facades\Auth;
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
        $teacherCourses = CourseTeacher::join('work_study_plan_courses_in_semesters', 'course_teacher.course_id', '=', 'work_study_plan_courses_in_semesters.external_id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('work_study_plan_course_control_types_in_semesters', 'work_study_plan_courses_in_semesters.id', '=', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('group_id', '=', $this->groupId)
            ->where('teacher_id', '=', Auth::user()->id)
            ->select(
                'course_teacher.id',
                'disciplines.name',
                'control_types.name as control_name',
                'work_study_plan_courses_in_semesters.lecture_hours',
                'work_study_plan_courses_in_semesters.practical_hours',
                'work_study_plan_courses_in_semesters.laboratory_hours',
                'work_study_plan_courses_in_semesters.semester_number'
            )
            ->get();

        return view('livewire.teacher-courses', ['teacherCourses' => $teacherCourses]);
    }

    public function destroy($id): void
    {
        $courseTeacher = CourseTeacher::findOrFail($id);
        $courseTeacher->delete();
    }

    public function showEJournal($courseTeacherId)
    {
        return redirect()->route('e.journal', ['courseTeacherId' => $courseTeacherId]);
    }
}
