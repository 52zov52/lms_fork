<?php

namespace App\Livewire;

use App\Http\Controllers\AcademicYearController;
use App\Models\StudentDocumentOrderIssuance;
use App\Models\StudentDocumentOrderRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StudentDocumentRequests extends Component
{
    use WithPagination;

    public $selectedDocumentType = 0, $employmentPlace = '';
    public $showModal = false;

    public $listDocumentTypes = [
        1 => 'Справка о статусе студента',
        2 => 'Справка о статусе студента (для транспортных карт) - только для студентов колледжа',
        3 => 'Справка-вызов только для студентов очно-заочной и заочной форм обучения'
    ];

    public function render()
    {
        $studentDocumentOrderRequests = StudentDocumentOrderRequest::where('requester_id', Auth::user()->id)->paginate(50);

        return view('livewire.student-document-requests', [
            'listDocumentTypes' => $this->listDocumentTypes,
            'studentDocumentRequests' => $studentDocumentOrderRequests
        ]);
    }

    public function callMethod($methodName)
    {
        $this->{$methodName}();
    }

    public function setDocumentType()
    {
        $this->selectedDocumentType = $this->selectedDocumentType;
    }

    public function sendRequest()
    {
        if ($this->selectedDocumentType != 0) {
            if ($this->selectedDocumentType == 3) {
                $this->showModal = true;
            } else {
                $this->store();
            }
        } else {
            session()->flash('type', 'error');
            session()->flash('message', 'Вы не выбрали тип документа!');
        }
    }

    public function store()
    {
        if ($this->selectedDocumentType == 3) {
            $this->validate([
                'employmentPlace' => 'required|string|max:1000'
            ]);
        }

        $studentDocumentOrderRequest = StudentDocumentOrderRequest::create([
            'document_type' => $this->selectedDocumentType,
            'status' => 1,
            'requester_id' => Auth::user()->id,
            'academic_year' => AcademicYearController::getAcademicYear(),
            'half_year' => AcademicYearController::getHalfYear()
        ]);

        if ($this->selectedDocumentType == 3) {
            StudentDocumentOrderIssuance::create([
                'student_document_order_request_id' => $studentDocumentOrderRequest->id,
                'employment_place' => $this->employmentPlace
            ]);

            $this->showModal = false;
        } else {
            StudentDocumentOrderIssuance::create([
                'student_document_order_request_id' => $studentDocumentOrderRequest->id
            ]);
        }

        session()->flash('type', 'success');
        session()->flash('message', 'Заявка успешно отправлена!');
    }
}
