<?php

namespace App\Livewire;

use App\Models\Group;
use App\Models\GroupStudent;
use Livewire\Component;

class Contingent extends Component
{
    public int $groupId = 0;
    public $contingent = null;

    public function render()
    {
        $groups = Group::select('id', 'group_name')->orderBy('group_name', 'asc')->get();

        return view('livewire.contingent', ['groups' => $groups, 'contingent' => $this->contingent]);
    }

    public function getContingent() : void
    {
        $this->contingent = GroupStudent::join('users_data', 'group_student.user_id', '=', 'users_data.user_id')
        ->join('student_ticket_numbers', 'group_student.user_id', '=', 'student_ticket_numbers.user_id')
            ->select(
                'group_student.id',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'student_ticket_numbers.ticket_number')
            ->where('group_student.group_id', '=', $this->groupId)
            ->orderBy('users_data.last_name', 'asc')
            ->get();
    }
}
