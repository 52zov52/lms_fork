<?php

namespace App\Livewire;

use App\Http\Controllers\ContingentController;
use App\Models\Group;
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

    public function callMethod($methodName)
    {
        $this->{$methodName}();
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
            session()->flash('type', 'error');
            session()->flash('message', 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!');
        }
    }
}
