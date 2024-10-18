<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Employee\ContingentController;
use App\Http\Controllers\Report\ContingentReportController;
use App\Models\Group;
use Livewire\Component;
use Illuminate\Support\Facades\Response;

class Contingent extends Component
{
    public int $groupId = 0;
    public $ticketNumber = '';
    public $groups = [];
    public $contingent = [];

    public function render()
    {
        $contingentController = new ContingentController();

        $this->groups = Group::select('id', 'group_name')
            ->whereIn('id', $contingentController->getGroupIds())
            ->orderBy('group_name', 'asc')
            ->get();

        if ($this->groupId != 0) {
            $this->contingent = $contingentController->getContingent($this->groupId);
        }else {
            if(!empty($this->ticketNumber)) {
                $this->contingent = $contingentController->searchContingentByTicketNumber($this->ticketNumber);
            }
        }

        return view('livewire.employee.contingent');
    }

    public function getContingent(): void
    {
        $this->ticketNumber = '';

        $contingentController = new ContingentController();
        $this->contingent = $contingentController->getContingent($this->groupId);
    }

    public function searchContingentByTicketNumber(): void
    {
        $this->groupId = 0;

        $contingentController = new ContingentController();
        $this->contingent = $contingentController->searchContingentByTicketNumber($this->ticketNumber);
    }

    public function downloadListOfStudents()
    {
        if ($this->groupId != 0) {
             $contingentReportController = new ContingentReportController();
             $data = $contingentReportController->downloadListOfStudents($this->groupId);

            return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!');
        }
    }
}
