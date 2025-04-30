<?php

namespace App\Livewire\Common;

use App\Http\Controllers\AcademicYearController;
use App\Models\AsyncInteractChat;
use App\Models\Discipline;
use App\Models\Group;
use App\Models\GroupStudent;
use App\Models\StudyThreadNormative;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsyncInteractChatList extends Component
{
    public $year = 0;
    public $halfYear = 0;
    public $disciplineId = 0;
    public $normativeId = 0;
    public $groupId = 0;

    public function mount($year, $halfYear, $disciplineId, $normativeId, $groupId)
    {
        $this->year = $year;
        $this->halfYear = $halfYear;
        $this->disciplineId = $disciplineId;
        $this->normativeId = $normativeId;
        $this->groupId = $groupId;
    }

    public function render()
    {
        $discipline = Discipline::where('id', '=', $this->disciplineId)
            ->select('name')
            ->first();

        $studyThreadNormative = StudyThreadNormative::where('id', '=', $this->normativeId)
            ->select('name')
            ->first();

        $group = Group::where('id', '=', $this->groupId)
            ->select('group_name')
            ->first();

        $students = GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
            ->where('group_id', '=', $this->groupId)
            ->select(
                'group_student.id',
                'group_student.user_id',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio")
            )
            ->orderBy('fio')
            ->get();

        return view('livewire.common.async-interact-chat-list', [
            'studyYear' => AcademicYearController::getAcademicYearByYear($this->year),
            'halfYear' => $this->halfYear,
            'discipline' => $discipline->name,
            'normative' => $studyThreadNormative->name,
            'group' => $group->group_name,
            'students' => $students
        ]);
    }

    public function redirectToChat($studentId)
    {
        $chat = AsyncInteractChat::where('year', '=', $this->year)
            ->where('half_year', '=', $this->halfYear)
            ->where('discipline_id', '=', $this->disciplineId)
            ->where('study_thread_normative_id', '=', $this->normativeId)
            ->where('group_id', '=', $this->groupId)
            ->where('user_id', '=', $studentId)
            ->where('role', '=', 'Student')
            ->select('id')
            ->first();

        if (is_null($chat)) {
            $this->dispatch('show-toast', message: 'Чат еще не создан', type: 'error');
        } else {
            return redirect()->route('teacher.async.interact.with.student.chat', ['chatId' => base64_encode($chat->id), 'mode' => 2]);
        }
    }
}
