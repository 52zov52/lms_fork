<?php

namespace App\Livewire;

use App\Models\Bulletin as ModelsBulletin;
use App\Models\StudentGrades;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Bulletin extends Component
{
    public $bulletin_id = 0;

    public function mount($bulletin_id)
    {
        $this->bulletin_id = $bulletin_id;
    }

    public function render()
    {
        $bulletin = ModelsBulletin::join('work_study_plan_course_control_types_in_semesters', 'bulletins.work_study_plan_course_control_types_in_semesters_id', '=', 'work_study_plan_course_control_types_in_semesters.id')
            ->join('control_types', 'work_study_plan_course_control_types_in_semesters.control_type_id', '=', 'control_types.id')
            ->join('work_study_plan_courses_in_semesters', 'work_study_plan_course_control_types_in_semesters.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->join('bulletin_teacher', 'bulletins.id', '=', 'bulletin_teacher.bulletin_id')
            ->join('users_data', 'bulletin_teacher.user_id', '=', 'users_data.user_id')
            ->where('bulletins.id', '=', $this->bulletin_id)
            ->select(
                'bulletins.id',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                'disciplines.name as discipline_name',
                'work_study_plan_courses_in_semesters.semester_number',
                'control_types.name as control_name',
                'bulletins.bulletin_number',
                'bulletins.type_bulletin_id',
                'bulletins.opening_date',
                'bulletins.closing_date'
            )
            ->first();
            
        $studentGrades = StudentGrades::join('users_data', 'student_grades.user_id', '=', 'users_data.user_id')
            ->where('student_grades.bulletin_id', '=', $this->bulletin_id)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("(SELECT ticket_number FROM student_ticket_numbers WHERE user_id = student_grades.user_id ORDER BY id DESC LIMIT 1) as ticket_number"),
                'student_grades.grade'
            )
            ->orderBy('fio')
            ->get();

        return view('livewire.bulletin', ['bulletin' => $bulletin, 'studentGrades' => $studentGrades]);
    }
}
