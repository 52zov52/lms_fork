<?php

namespace App\Livewire\Teacher;

use App\Models\ActiveUser;
use App\Models\TeacherAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InteractWithStudent extends Component
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
        return view('livewire.teacher.interact-with-student', ['data' => $this->getData()]);
    }

    public function redirectToUploadMaterials($studyYear, $halfYear, $disciplineId, $normativeId, $groupId)
    {
        $teacherAssignment = TeacherAssignment::where('study_year', '=', $studyYear)
            ->where('half_year', '=', $halfYear)
            ->where('discipline_id', '=', $disciplineId)
            ->where('study_thread_normative_id', '=', $normativeId)
            ->where('group_id', '=', $groupId)
            ->where('teacher_id', '=', Auth::user()->id)
            ->select('id')
            ->first();

        if (is_null($teacherAssignment)) {
            $teacherAssignment = TeacherAssignment::create([
                'study_year' => $studyYear,
                'half_year' => $halfYear,
                'discipline_id' => $disciplineId,
                'study_thread_normative_id' => $normativeId,
                'group_id' => $groupId,
                'teacher_id' => Auth::user()->id
            ]);
        }

        return redirect()->route('teacher.interact.upload.materials', ['id' => $teacherAssignment->id]);
    }

    private function getData()
    {
        $year = $this->getStudyYear();

        return DB::table('study_threads')
            ->join('study_thread_subgroups', 'study_threads.id', '=', 'study_thread_subgroups.study_thread_id')
            ->join('study_thread_subgroup_user', 'study_thread_subgroups.id', '=', 'study_thread_subgroup_user.study_thread_subgroup_id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroup_user.study_thread_subgroup_id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('chair_discipline', 'study_threads.chair_discipline_id', '=', 'chair_discipline.id')
            ->join('disciplines', 'chair_discipline.discipline_id', '=', 'disciplines.id')
            ->join('study_thread_normatives', 'study_threads.study_thread_normative_id', '=', 'study_thread_normatives.id')
            ->where('study_threads.year', $year)
            ->whereIn('study_thread_subgroup_user.user_id', $this->listUserId)
            ->whereNotIn('study_threads.study_thread_normative_id', [1, 2, 23, 24, 26, 27, 28, 29, 30])
            ->select(
                'study_threads.id',
                'study_threads.year',
                DB::raw("CONCAT(study_threads.year,'/',study_threads.year + 1) AS study_year"),
                'study_threads.semester as half_year',
                'groups.id as group_id',
                'groups.group_name',
                'disciplines.id as discipline_id',
                'disciplines.name as discipline_name',
                'study_thread_normatives.id as normative_id',
                'study_thread_normatives.name as normative_name'
            )
            ->orderBy('half_year')
            ->orderBy('discipline_name')
            ->orderBy('group_name')
            ->orderBy('study_thread_normatives.sort_order')
            ->get();
    }

    private function getStudyYear()
    {
        $currentYear = now()->format('Y');
        $currentMonth = now()->format('m');

        if ($currentMonth >= 9 && $currentMonth <= 12) {
            return $currentYear;
        } else {
            return Carbon::parse($currentYear)->subYear()->format('Y');
        }
    }
}
