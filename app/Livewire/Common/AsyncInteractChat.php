<?php

namespace App\Livewire\Common;

use App\Http\Controllers\AcademicYearController;
use App\Models\ActiveUser;
use App\Models\AsyncInteractChat as ModelsAsyncInteractChat;
use App\Models\GroupStudent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsyncInteractChat extends Component
{
    public $listUserId = array();
    public $groupId = 0;

    public $data = [];

    public function mount()
    {
        if (Auth::user()->hasRole('Student')) {
            $groupStudent = GroupStudent::where('user_id', '=', Auth::user()->id)->select('group_id')->first();
            $this->groupId = $groupStudent->group_id;
        } else {
            $activeUser = ActiveUser::where('user_id', Auth::user()->id)->select('list_user_id')->first();

            if (!is_null($activeUser)) {
                $this->listUserId = json_decode($activeUser->list_user_id)->id;
            } else {
                $this->listUserId = array(Auth::user()->id);
            }
        }
    }

    public function render()
    {
        $this->data = (Auth::user()->hasRole('Student')) ? $this->getDataForStudent() : $this->getDataForTeacher();

        return view('livewire.common.async-interact-chat', [
            'data' => $this->data
        ]);
    }

    private function getDataForTeacher()
    {
        return DB::table('study_threads')
            ->join('study_thread_subgroups', 'study_threads.id', '=', 'study_thread_subgroups.study_thread_id')
            ->join('study_thread_subgroup_user', 'study_thread_subgroups.id', '=', 'study_thread_subgroup_user.study_thread_subgroup_id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroup_user.study_thread_subgroup_id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('chair_discipline', 'study_threads.chair_discipline_id', '=', 'chair_discipline.id')
            ->join('disciplines', 'chair_discipline.discipline_id', '=', 'disciplines.id')
            ->join('study_thread_normatives', 'study_threads.study_thread_normative_id', '=', 'study_thread_normatives.id')
            ->where('study_threads.year', AcademicYearController::getYear())
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

    private function getDataForStudent()
    {
        return DB::table('study_threads')
            ->join('study_thread_subgroups', 'study_threads.id', '=', 'study_thread_subgroups.study_thread_id')
            ->join('group_study_thread_subgroup', 'study_thread_subgroups.id', '=', 'group_study_thread_subgroup.study_thread_subgroup_id')
            ->join('study_thread_subgroup_user', 'study_thread_subgroups.id', '=', 'study_thread_subgroup_user.study_thread_subgroup_id')
            ->join('users_data', 'study_thread_subgroup_user.user_id', '=', 'users_data.user_id')
            ->join('groups', 'group_study_thread_subgroup.group_id', '=', 'groups.id')
            ->join('chair_discipline', 'study_threads.chair_discipline_id', '=', 'chair_discipline.id')
            ->join('disciplines', 'chair_discipline.discipline_id', '=', 'disciplines.id')
            ->join('study_thread_normatives', 'study_threads.study_thread_normative_id', '=', 'study_thread_normatives.id')
            ->where('study_threads.year', AcademicYearController::getYear())
            ->where('group_study_thread_subgroup.group_id', $this->groupId)
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

    public function redirectToFiles($year, $halfYear, $disciplineId, $normativeId, $groupId)
    {
        if (Auth::user()->hasRole('Student')) {
            $chat = ModelsAsyncInteractChat::where('year', '=', $year)
                ->where('half_year', '=', $halfYear)
                ->where('discipline_id', '=', $disciplineId)
                ->where('study_thread_normative_id', '=', $normativeId)
                ->where('group_id', '=', $groupId)
                ->where('role', '=', 'Teacher')
                ->select('id')
                ->first();

            if (is_null($chat)) {
                $this->dispatch('show-toast', message: 'Чат еще не создан', type: 'error');
            } else {
                return redirect()->route('student.async.interact.chat.view.files', ['chatId' => base64_encode($chat->id)]);
            }
        } else {
            $chat = ModelsAsyncInteractChat::where('year', '=', $year)
                ->where('half_year', '=', $halfYear)
                ->where('discipline_id', '=', $disciplineId)
                ->where('study_thread_normative_id', '=', $normativeId)
                ->where('group_id', '=', $groupId)
                ->where('user_id', '=', Auth::user()->id)
                ->select('id')
                ->first();

            if (is_null($chat)) {
                $chat = $this->createChat($year, $halfYear, $disciplineId, $normativeId, $groupId, 'Teacher');
            }

            return redirect()->route('teacher.async.interact.chat.upload.files', ['chatId' => base64_encode($chat->id), 'mode' => 1]);
        }
    }

    public function redirectToChats($year, $halfYear, $disciplineId, $normativeId, $groupId)
    {
        if (Auth::user()->hasRole('Student')) {
            $chat = ModelsAsyncInteractChat::where('year', '=', $year)
                ->where('half_year', '=', $halfYear)
                ->where('discipline_id', '=', $disciplineId)
                ->where('study_thread_normative_id', '=', $normativeId)
                ->where('group_id', '=', $groupId)
                ->where('user_id', '=', Auth::user()->id)
                ->select('id')
                ->first();

            if (is_null($chat)) {
                $chat = $this->createChat($year, $halfYear, $disciplineId, $normativeId, $groupId, 'Student');
            }

            return redirect()->route('student.async.interact.with.teacher.chat', ['chatId' => base64_encode($chat->id), 'mode' => 2]);
        } else {
            return redirect()->route('teacher.async.interact.chat.list', [
                'year' => $year,
                'halfYear' => $halfYear,
                'disciplineId' => $disciplineId,
                'normativeId' => $normativeId,
                'groupId' => $groupId
            ]);
        }
    }

    private function createChat($year, $halfYear, $disciplineId, $normativeId, $groupId, $role)
    {
        return ModelsAsyncInteractChat::create([
            'year' => $year,
            'half_year' => $halfYear,
            'discipline_id' => $disciplineId,
            'study_thread_normative_id' => $normativeId,
            'group_id' => $groupId,
            'user_id' => Auth::user()->id,
            'role' => $role
        ]);
    }
}
