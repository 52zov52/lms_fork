<?php

namespace App\Livewire\Teacher;

use App\Http\Controllers\AcademicYearController;
use App\Models\ActiveUser;
use App\Models\StudyThreadSubGroupUser;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyGroups extends Component
{
    protected $listUserId = array();

    public function mount()
    {
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

        $listOfGroups = StudyThreadSubGroupUser::select('groups.id', 'groups.group_name', 'groups.course', 'specialities.level_education_id', 'work_study_plans.teaching_form_id')
            ->join('study_thread_subgroups', 'study_thread_subgroup_user.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('specialities', 'work_study_plans.speciality_id', '=', 'specialities.id')
            ->where('study_threads.year', $currentYear)
            ->whereIn('study_thread_subgroup_user.user_id', $this->listUserId)
            ->orderBy('specialities.level_education_id')
            ->orderBy('groups.course')
            ->distinct()
            ->get();

        return view('livewire.teacher.my-groups', [
            'academicYear' => AcademicYearController::getAcademicYear(),
            'listOfGroups' => $listOfGroups
        ]);
    }

    public function redirectToCourseList($groupId)
    {
        return redirect()->route('teacher.course.list', ['groupId' => $groupId]);
    }
}
