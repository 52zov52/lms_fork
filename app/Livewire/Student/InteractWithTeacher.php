<?php

namespace App\Livewire\Student;

use App\Models\GroupStudent;
use App\Models\TeacherAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InteractWithTeacher extends Component
{
    public function render()
    {
        $groupStudent = GroupStudent::where('user_id', '=', Auth::user()->id)->select('group_id')->first();

        return view('livewire.student.interact-with-teacher', ['data' => $this->getData($groupStudent->group_id)]);
    }

    public function redirectToViewMaterials($studyYear, $halfYear, $disciplineId, $normativeId, $groupId)
    {
        $teacherAssignment = TeacherAssignment::where('study_year', '=', $studyYear)
            ->where('half_year', '=', $halfYear)
            ->where('discipline_id', '=', $disciplineId)
            ->where('study_thread_normative_id', '=', $normativeId)
            ->where('group_id', '=', $groupId)
            ->select('id')
            ->first();

        return redirect()->route('student.interact.view.materials', ['id' => $teacherAssignment->id]);
    }

    private function getData($groupId)
    {
        $year = $this->getStudyYear();

        return DB::table('study_threads')
            ->join('study_thread_subgroups', 'study_threads.id', '=', 'study_thread_subgroups.study_thread_id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('study_thread_subgroup_user', 'study_thread_subgroups.id', '=', 'study_thread_subgroup_user.study_thread_subgroup_id')
            ->join('users_data', 'study_thread_subgroup_user.user_id', '=', 'users_data.user_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('chair_discipline', 'study_threads.chair_discipline_id', '=', 'chair_discipline.id')
            ->join('disciplines', 'chair_discipline.discipline_id', '=', 'disciplines.id')
            ->join('study_thread_normatives', 'study_threads.study_thread_normative_id', '=', 'study_thread_normatives.id')
            ->where('study_threads.year', $year)
            ->where('group_study_thread_subgroup.group_id', $groupId)
            ->whereNotIn('study_threads.study_thread_normative_id', [1, 2, 23, 24, 26, 27, 28, 29, 30])
            ->select(
                'study_threads.id',
                'study_threads.year',
                DB::raw("CONCAT(study_threads.year,'/',study_threads.year + 1) AS study_year"),
                'study_threads.semester as half_year',
                'groups.id as group_id',
                'groups.group_name',
                DB::raw("CONCAT(users_data.last_name, ' ', SUBSTRING(users_data.first_name, 1, 1), '. ', SUBSTRING(users_data.middle_name, 1, 1), '.') as teacher_fio"),
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
