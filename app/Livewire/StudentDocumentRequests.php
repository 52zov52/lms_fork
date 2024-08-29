<?php

namespace App\Livewire;

use App\Http\Controllers\AcademicYearController;
use App\Models\StudentDocumentRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDocumentRequests extends Component
{
    public $documentType = 0;
    public $showModal = false;

    public $listDocumentTypes = [
        1 => 'Справка о статусе студента',
        2 => 'Справка о статусе студента (для транспортных карт) - только для студентов колледжа',
        3 => 'Справка-вызов только для студентов очно-заочной и заочной форм обучения'
    ];

    public function render()
    {
        return view('livewire.student-document-requests', ['listDocumentTypes' => $this->listDocumentTypes]);
    }

    public function callMethod($methodName)
    {
        $this->{$methodName}();
    }

    public function setDocumentType()
    {
        $this->documentType = $this->documentType;
    }

    public function sendRequest()
    {
        if ($this->documentType != 0) {
            if ($this->documentType == 3) {
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
        StudentDocumentRequest::create([
            'document_type' => $this->documentType,
            'status' => 1,
            'requester_id' => Auth::user()->id,
            'academic_year' => AcademicYearController::getAcademicYear()
        ]);

        if ($this->documentType == 3) {
            $this->showModal = false;
        }

        session()->flash('type', 'success');
        session()->flash('message', 'Заявка успешно отправлена!');
    }
}
