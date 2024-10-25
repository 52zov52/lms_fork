<?php

namespace App\Livewire\Teacher;

use App\Http\Controllers\AcademicYearController;
use App\Models\ActiveUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        $groups = DB::table('study_thread_subgroup_user')
            ->join('study_thread_subgroups', 'study_thread_subgroup_user.study_thread_subgroup_id', '=', 'study_thread_subgroups.id')
            ->join('study_threads', 'study_thread_subgroups.study_thread_id', '=', 'study_threads.id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->whereIn('study_thread_subgroup_user.user_id', $this->listUserId)
            ->where('study_threads.year', $currentYear)
            ->distinct()
            ->select('groups.id', 'groups.group_name')
            ->get();

        return view('livewire.teacher.my-groups', [
            'academicYear' => AcademicYearController::getAcademicYear(),
            'groups' => $groups
        ]);
    }
}
