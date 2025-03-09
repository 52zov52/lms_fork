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
    public $groups = [];
    public $contingent = [];
    public $docNumber = '';

    public function render()
    {
        $contingentController = new ContingentController();

        $this->groups = Group::select('id', 'group_name')
            ->whereIn('id', $contingentController->getGroupIds())
            ->orderBy('group_name', 'asc')
            ->get();

        if ($this->selectedGroupId != 0) {
            $this->contingent = $contingentController->getContingent($this->selectedGroupId, [0]);
        }

        return view('livewire.employee.milatary');
    }

    public function getContingent(): void
    {
        $contingentController = new ContingentController();
        $this->contingent = $contingentController->getContingent($this->selectedGroupId, [0]);
    }

    public function milatary($userId)
    {
        $report = new PrintDefenseMinistryForm10Controller();
        $data = $report->print($userId);

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend();
    }

    public function form4($userId) 
    {
        if($this->docNumber != '') {
            $report = new PrintDefenseMinistryForm10Controller();
            $data = $report->form4($userId, $this->selectedGroupId, $this->docNumber);
    
            return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend();
        }else {
            $this->dispatch('show-toast', message: 'Поле номер документа должно быть заполнено!', type: 'error');
        }
    }
}
