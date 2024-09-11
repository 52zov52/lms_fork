<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
use Livewire\Component;

class Contingent extends Component
{
    public int $groupId = 0;
    public $contingent = null;
    public $ticketNumber = "";

    public function render()
    {
        $contingents = new ContingentController();
        $groups = Group::select('id', 'group_name')->whereIn('id', $contingents->getGroupIds())->orderBy('group_name', 'asc')->get();

        return view('livewire.contingent', [
            'groups' => $groups,
            'contingent' => $this->contingent
        ]);
    }

    public function callMethod($methodName)
    {
        $this->{$methodName}();
    }

    public function getContingent(): void
    {
        $contingents = new ContingentController();
        $this->contingent = $contingents->getContingent($this->groupId);
    }

    public function searchContingentByTicketNumbers()
    {
        if ($this->ticketNumber != "") {
            $contingents = new ContingentController();
            $this->contingent = $contingents->searchContingentByTicketNumbers($this->ticketNumber);
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Для поиска необходимо ввести № зачетной книжки!');
        }
    }

    public function generateListOfStudentsInGroupReport()
    {
        if ($this->groupId != 0) {
            return redirect()->route('generate.list.of.student.in.group.report', ['groupId' => $this->groupId]);
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!');
        }
    }

    public function getIndividualStudentGradeReport($userId) 
    {
        return redirect()->route('individual.student.grade.report', ['userId' => $userId]);
    }

    public function printDefenseMinistryForm10($userId)
    {
        return redirect()->route('print.defense.ministry.form.10', ['userId' => $userId]);
    }
}
