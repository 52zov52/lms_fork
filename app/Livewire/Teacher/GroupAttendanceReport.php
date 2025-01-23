<?php

namespace App\Livewire\Teacher;

use App\Http\Controllers\AcademicYearController;
use App\Models\EJournal;
use App\Models\Group;
use App\Models\WorkStudyPlanCoursesInSemester;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class GroupAttendanceReport extends Component
{
    public $wspCoursesInSemesterId;
    public $groupId;

    public function mount($wspCoursesInSemesterId, $groupId)
    {
        $this->wspCoursesInSemesterId = $wspCoursesInSemesterId;
        $this->groupId = $groupId;
    }

    public function render()
    {
        $group = Group::select('group_name')->where('id', '=', $this->groupId)->first();
        $wspCoursesInSemester = WorkStudyPlanCoursesInSemester::select('disciplines.name as discipline_name')
            ->join('work_study_plan_courses', 'work_study_plan_courses_in_semesters.work_study_plan_course_id', '=', 'work_study_plan_courses.id')
            ->join('disciplines', 'work_study_plan_courses.discipline_id', '=', 'disciplines.id')
            ->where('work_study_plan_courses_in_semesters.id', '=', $this->wspCoursesInSemesterId)
            ->first();

        $aboutYear = EJournal::where('work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('group_id', '=', $this->groupId)
            ->select('academic_year', 'half_year')
            ->first();

        $eJournal = EJournal::join('users_data', 'e_journals.student_id', '=', 'users_data.user_id')
            ->where('e_journals.work_study_plan_courses_in_semester_id', '=', $this->wspCoursesInSemesterId)
            ->where('e_journals.group_id', '=', $this->groupId)
            ->select(
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("SUM(CASE WHEN e_journals.status = 1 OR e_journals.status = 6 OR e_journals.status = 7 OR e_journals.status = 8 OR e_journals.status = 9 OR e_journals.status = 10 THEN 1 ELSE 0 END) as was_present"),
                DB::raw("(COUNT(e_journals.status) - SUM(CASE WHEN e_journals.status = 1 OR e_journals.status = 6 OR e_journals.status = 7 OR e_journals.status = 8 OR e_journals.status = 9 OR e_journals.status = 10 THEN 1 ELSE 0 END)) as was_absent"),
                DB::raw("COUNT(e_journals.status) as total")
            )
            ->groupBy('fio')
            ->get();

        $academicYear = $aboutYear->academic_year;
        $halfYear = $aboutYear->half_year;

        return view('livewire.teacher.group-attendance-report', [
            'group_name' => $group->group_name,
            'discipline_name' => $wspCoursesInSemester->discipline_name,
            'academicYear' => $academicYear,
            'halfYear' => $halfYear,
            'eJournal' => $eJournal
        ]);
    }
}
