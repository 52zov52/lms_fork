<?php

namespace App\Livewire\Teacher;

use App\Http\Controllers\AcademicYearController;
use App\Models\ActiveUser;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CourseList extends Component
{
    public $groupId = 0;
    protected $listUserId = array();

    public function mount($groupId)
    {
        $this->groupId = $groupId;

        $activeUser = ActiveUser::where('user_id', Auth::user()->id)->select('list_user_id')->first();

        if (!is_null($activeUser)) {
            $this->listUserId = json_decode($activeUser->list_user_id)->id;
        } else {
            $this->listUserId = array(Auth::user()->id);
        }
    }

    public function render()
    {
        $currentYear = AcademicYearController::getYear();
        $group = Group::select('group_name')->where('id', '=', $this->groupId)->first();

        $courseList = DB::table('group_study_thread_subgroup')
            ->select(
                'study_threads.name',
                'study_threads.semester as half_year',
                'work_study_plan_courses_in_semesters.semester_number',
                'work_study_plan_courses_in_semesters.id as wsp_courses_in_semester_id',
            )
            ->join('study_thread_subgroups', 'group_study_thread_subgroup.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('study_thread_subgroup_user', 'study_thread_subgroups.id', '=', 'study_thread_subgroup_user.study_thread_subgroup_id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('work_study_plan_courses_in_semesters', 'group_study_thread_subgroup.work_study_plan_courses_in_semester_id', '=', 'work_study_plan_courses_in_semesters.id')
            ->whereIn('study_thread_subgroup_user.user_id', $this->listUserId)
            ->where('study_threads.year', '=', $currentYear)
            ->where('group_study_thread_subgroup.group_id', '=', $this->groupId)
            ->orderBy('study_threads.name')
            ->orderBy('study_threads.semester')
            ->distinct()
            ->get();

        return view('livewire.teacher.course-list', [
            'group_name' => $group->group_name,
            'courseList' => $courseList
        ]);
    }

    public function redirectToEJournal($wspCoursesInSemesterId)
    {
        return redirect()->route('teacher.e.journal', [
            'wspCoursesInSemesterId' => $wspCoursesInSemesterId,
            'groupId' => $this->groupId
        ]);
    }

    public function groupAttendanceReport($wspCoursesInSemesterId)
    {
        return redirect()->route('teacher.e.journal.group.attendance.report', [
            'wspCoursesInSemesterId' => $wspCoursesInSemesterId,
            'groupId' => $this->groupId
        ]);
    }
}
