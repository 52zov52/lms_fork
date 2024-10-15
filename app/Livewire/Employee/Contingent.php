<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Employee\ContingentController;
use App\Models\Group;
use Livewire\Component;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;

class Contingent extends Component
{
    public int $groupId = 0;
    public $groups = [];
    public $contingent = [];

    public function mount()
    {
        $contingentController = new ContingentController();

        $this->groups = Group::select('id', 'group_name')
            ->whereIn('id', $contingentController->getGroupIds())
            ->orderBy('group_name', 'asc')
            ->get();
    }

    public function render()
    {
        if ($this->groupId != 0) {
            $contingentController = new ContingentController();
            $this->contingent = $contingentController->getContingent($this->groupId);
        }


        return view('livewire.employee.contingent');
    }

    public function callMethod($methodName, $param = null): void
    {
        $this->{$methodName}($param);
    }

    public function getContingent(): void
    {
        $contingentController = new ContingentController();
        $this->contingent = $contingentController->getContingent($this->groupId);
    }

    public function downloadListOfStudents()
    {
        if ($this->groupId != 0) {
            return redirect()->route('employee.contingent.report.download.list.of.students', ['groupId' => $this->groupId]);
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!');
        }
    }
}
