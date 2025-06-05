<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Employee\ContingentController;
use App\Http\Controllers\Reports\PrintDefenseMinistryForm10Controller;
use App\Models\Group;
use Illuminate\Support\Facades\Response;
use Livewire\Component;

class Milatary extends Component
{
    public int $selectedGroupId = 0;
    public int $selectedUserId = 0;
    public $ticketNumber = '';
    public $groups = [];
    public $contingent = [];
    public $dateFrom = '';
    public $docNumber = '';

    public bool $showForm4ParamsModal = false;

    public function render()
    {
        $contingentController = new ContingentController();

        $this->groups = Group::select('id', 'group_name')
            ->whereIn('id', $contingentController->getGroupIds())
            ->orderBy('group_name', 'asc')
            ->get();

        if ($this->selectedGroupId != 0) {
            $this->contingent = $contingentController->getContingent($this->selectedGroupId);
        } else {
            if (!empty($this->ticketNumber)) {
                $this->contingent = $contingentController->searchContingentByTicketNumber($this->ticketNumber);
            }
        }

        return view('livewire.employee.milatary');
    }

    public function toggleForm4ParamsModal($userId): void
    {
        $this->selectedUserId = $userId;
        $this->showForm4ParamsModal = !$this->showForm4ParamsModal;
    }

    public function getContingent(): void
    {
        $this->ticketNumber = '';

        $contingentController = new ContingentController();
        $this->contingent = $contingentController->getContingent($this->selectedGroupId, [0]);
    }

    public function searchContingentByTicketNumber(): void
    {
        $this->selectedGroupId = 0;

        $contingentController = new ContingentController();
        $this->contingent = $contingentController->searchContingentByTicketNumber($this->ticketNumber);
    }

    public function printForm10($userId)
    {
        $this->selectedUserId = $userId;

        $report = new PrintDefenseMinistryForm10Controller();
        $data = $report->print($this->selectedUserId);

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend();
    }

    public function printForm4()
    {
        $this->validate([
            'dateFrom' => 'required',
            'docNumber' => 'required'
        ]);

        try {
            $report = new PrintDefenseMinistryForm10Controller();
            $data = $report->form4($this->selectedUserId, $this->selectedGroupId, $this->dateFrom, $this->docNumber);

            return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend();
        } catch (\Throwable $th) {
            $this->dispatch('show-toast', message: 'Произошла ошибка в процессе формировани справки!', type: 'error');
        }
    }
}
