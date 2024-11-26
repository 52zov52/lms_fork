<?php

namespace App\Livewire\Student;

use App\Models\ConsultationSchedule;
use App\Models\GroupStudent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyConsultationSchedule extends Component
{
    public $groupId = 0;

    public function mount()
    {
        $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
            ->where('group_student.user_id', '=', Auth::user()->id)
            ->select('groups.id')
            ->orderBy('group_student.id', 'desc')
            ->first();

        $this->groupId = $groupStudent->id;
    }

    public function render()
    {
        $consultationSchedules = ConsultationSchedule::join('disciplines', 'consultation_schedules.discipline_id', '=', 'disciplines.id')
            ->whereJsonContains('consultation_schedules.groups', ['id' => $this->groupId])
            ->select(
                'consultation_schedules.id',
                'disciplines.name',
                'consultation_schedules.consultation_date',
                'consultation_schedules.consultation_time',
                'consultation_schedules.room',
                'consultation_schedules.description'
            )
            ->paginate(50);

        return view('livewire.student.my-consultation-schedule', [
            'consultationSchedules' => $consultationSchedules
        ]);
    }
}
