<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use App\Models\StudentsAcademicLeave;
use Carbon\Carbon;
use Livewire\Component;

class Contingent extends Component
{
    public int $groupId = 0;
    public $contingent = null;

    public function render()
    {
        $contingents = new ContingentController();
        $groupId = $contingents->groupId;

        $groups = Group::select('id', 'group_name')->whereIn('id', $groupId)->orderBy('group_name', 'asc')->get();
        

        return view('livewire.contingent', [
            'groups' => $groups,
            'contingent' => $this->contingent
        ]);
    }

    public function getContingent(): void
    {
        $contingents = new ContingentController();
        $this->contingent = $contingents->getContingent($this->groupId);
    }

    public function generateListOfStudentsInGroupReport()
    {
        if($this->groupId != 0) {
            return redirect()->route('generate.list.of.student.in.group.report', ['groupId' => $this->groupId]);
        }
        else {
            session()->flash('message-error', 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!');
        }
    }
}
