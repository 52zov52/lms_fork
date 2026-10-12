<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Employee\ContingentController;
use App\Http\Controllers\Report\StudentListReportController;
use App\Http\Controllers\Report\GroupStudentGradeController;
use App\Http\Controllers\Report\IndividualStudentGradeController;
use App\Models\Group;
use Livewire\Component;
use Illuminate\Support\Facades\Response;

class Contingent extends Component
{
    public int $selectedGroupId = 0;
    public $ticketNumber = '';
    public $groups = [];
    public $contingent = [];

    // Окно выбора формата скачивания списка студентов
    public bool $isShowDownloadModal = false;
    public string $downloadFormat = 'xlsx';   // xlsx | docx | pdf
    public bool $minimalData = false;         // true - только № пп, ФИО, № ст. билета

    public function render()
    {
        $contingentController = new ContingentController();

        $this->groups = Group::select('id', 'group_name')
            ->whereIn('id', $contingentController->getGroupIds())
            ->orderBy('group_name', 'asc')
            ->get();

        if ($this->selectedGroupId != 0) {
            $this->contingent = $contingentController->getContingentWithMilitaryStatus($this->selectedGroupId);
        } else {
            if (!empty($this->ticketNumber)) {
                $this->contingent = $contingentController->searchContingentByTicketNumber($this->ticketNumber);
            }
        }

        return view('livewire.employee.contingent');
    }

    public function getContingent(): void
    {
        $this->ticketNumber = '';

        $contingentController = new ContingentController();
        $this->contingent = $contingentController->getContingentWithMilitaryStatus($this->selectedGroupId);
    }

    public function searchContingentByTicketNumber(): void
    {
        $this->selectedGroupId = 0;

        $contingentController = new ContingentController();
        $this->contingent = $contingentController->searchContingentByTicketNumber($this->ticketNumber);
    }

    public function openDownloadModal(): void
    {
        if ($this->selectedGroupId == 0) {
            $this->dispatch('show-toast', message: 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!', type: 'error');
            return;
        }

        $this->isShowDownloadModal = true;
    }

    public function downloadListOfStudents()
    {
        if ($this->selectedGroupId == 0) {
            $this->isShowDownloadModal = false;
            $this->dispatch('show-toast', message: 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!', type: 'error');
            return;
        }

        $format = in_array($this->downloadFormat, StudentListReportController::FORMATS, true)
            ? $this->downloadFormat
            : 'xlsx';

        try {
            $data = (new StudentListReportController())->download($this->selectedGroupId, $format, $this->minimalData);
        } catch (\Throwable $e) {
            report($e);
            $this->isShowDownloadModal = false;
            $this->dispatch('show-toast', message: 'Не удалось сформировать список. Попробуйте другой формат или обратитесь к администратору.', type: 'error');
            return;
        }

        $this->isShowDownloadModal = false;

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
    }

    public function downloadIndividualStudentGrade($userId)
    {
        $individualStudentGradeController = new IndividualStudentGradeController();
        $data = $individualStudentGradeController->print($userId);

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
    }

    public function downloadGroupStudentGrade()
    {
        if ($this->selectedGroupId != 0) {
            $groupStudentGradeController = new GroupStudentGradeController();
            $data = $groupStudentGradeController->print($this->selectedGroupId);

            return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
        } else {
            $this->dispatch('show-toast', message: 'Невозможно сформировать отчет, т.к. не выбрана группа из списка!', type: 'error');
        }
    }
}