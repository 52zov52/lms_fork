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
        $teacherCourses = CourseTeacher::join('study_plan_courses_in_semesters', 'course_teachers.course_id', '=', 'study_plan_courses_in_semesters.id')
            ->join('study_plan_courses', 'study_plan_courses_in_semesters.study_plan_course_id', '=', 'study_plan_courses.id')
            ->join('disciplines', 'study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('group_id', '=', $this->groupId)
            ->where('teacher_id', '=', Auth::user()->id)
            ->select(
                'course_teachers.id',
                'disciplines.name',
                'study_plan_courses_in_semesters.lecture_hours',
                'study_plan_courses_in_semesters.practical_hours',
                'study_plan_courses_in_semesters.laboratory_hours',
                'study_plan_courses_in_semesters.semester_number'
            )
            ->get();

        return view('livewire.teacher-courses', ['teacherCourses' => $teacherCourses]);
    }

    public function showEJournal($courseTeacherId)
    {
        
    }
}
