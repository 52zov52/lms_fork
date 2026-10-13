<?php

namespace App\Livewire;

use App\Http\Controllers\AcademicYearController;
use App\Models\StudentDocumentOrderIssuance;
use App\Models\StudentDocumentOrderRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class StudentDocumentRequests extends Component
{
    use WithPagination;

    public $quantity = 1;
    public $selectedDocumentType = 0, $employmentPlace = '';
    public $showModal = false;

    // Вид характеристики выбирает студент: 'positive' | 'negative' | '' (не выбран)
    public string $characteristicType = '';

    public $listDocumentTypes = [
        1 => 'Справка о статусе студента',
        2 => 'Справка о статусе студента (для транспортных карт) - только для студентов колледжа',
        3 => 'Справка-вызов только для студентов очно-заочной и заочной форм обучения',
        4 => 'Характеристика'
    ];

    public function render()
    {
        // Вид характеристики хранится в выдаче (issuances), поэтому подтягиваем его join'ом
        $studentDocumentOrderRequests = StudentDocumentOrderRequest::leftJoin(
                'student_document_order_issuances',
                'student_document_order_issuances.student_document_order_request_id',
                '=',
                'student_document_order_requests.id'
            )
            ->where('student_document_order_requests.requester_id', Auth::user()->id)
            ->select(
                'student_document_order_requests.*',
                'student_document_order_issuances.characteristic_type'
            )
            ->orderByDesc('student_document_order_requests.created_at')
            ->orderByDesc('student_document_order_requests.id')
            ->paginate(50);

        return view('livewire.student-document-requests', [
            'listDocumentTypes' => $this->listDocumentTypes,
            'studentDocumentRequests' => $studentDocumentOrderRequests
        ]);
    }

    // Хук Livewire: вызывается при изменении selectedDocumentType
    public function updatedSelectedDocumentType(): void
    {
        if ((int) $this->selectedDocumentType !== 4) {
            $this->characteristicType = '';
        }

        $this->resetErrorBag('characteristicType');
    }

    public function callMethod($methodName)
    {
        $this->{$methodName}();
    }

    // Оставлен для совместимости со старым blade (wire:change="setDocumentType")
    public function setDocumentType(): void
    {
        if ((int) $this->selectedDocumentType !== 4) {
            $this->characteristicType = '';
        }
    }

    public function sendRequest()
    {
        if ((int) $this->selectedDocumentType === 0) {
            session()->flash('type', 'error');
            session()->flash('message', 'Вы не выбрали тип документа!');
            return;
        }

        $rules = ['quantity' => 'required|integer|min:1|max:10'];

        if ((int) $this->selectedDocumentType === 4) {
            $rules['characteristicType'] = ['required', Rule::in(['positive', 'negative'])];
        }

        $messages = [
            'characteristicType.required' => 'Выберите вид характеристики: положительная или отрицательная',
            'characteristicType.in'       => 'Неверный вид характеристики',
        ];

        $this->validate($rules, $messages);

        if ((int) $this->selectedDocumentType === 3) {
            $this->showModal = true;
        } else {
            $this->store();
        }
    }

    public function store()
    {
        $rules = [
            'selectedDocumentType' => ['required', Rule::in(array_keys($this->listDocumentTypes))],
            'quantity' => 'required|integer|min:1|max:10',
        ];

        if ((int) $this->selectedDocumentType === 3) {
            $rules['employmentPlace'] = 'required|string|max:1000';
        }

        if ((int) $this->selectedDocumentType === 4) {
            $rules['characteristicType'] = ['required', Rule::in(['positive', 'negative'])];
        }

        $messages = [
            'characteristicType.required' => 'Выберите вид характеристики: положительная или отрицательная',
            'characteristicType.in'       => 'Неверный вид характеристики',
        ];

        $this->validate($rules, $messages);

        $studentDocumentOrderRequest = StudentDocumentOrderRequest::create([
            'document_type' => $this->selectedDocumentType,
            'status' => 1,
            'requester_id' => Auth::user()->id,
            'academic_year' => AcademicYearController::getAcademicYear(),
            'half_year' => AcademicYearController::getHalfYear(),
            'quantity' => (int) $this->quantity,
        ]);

        if ((int) $this->selectedDocumentType === 3) {
            StudentDocumentOrderIssuance::create([
                'student_document_order_request_id' => $studentDocumentOrderRequest->id,
                'employment_place' => $this->employmentPlace
            ]);

            $this->showModal = false;
        } else {
            StudentDocumentOrderIssuance::create([
                'student_document_order_request_id' => $studentDocumentOrderRequest->id,
                'characteristic_type' => (int) $this->selectedDocumentType === 4
                    ? $this->characteristicType
                    : 'positive',
            ]);
        }

        // Сбрасываем выбор, чтобы для следующей заявки студент снова выбрал вид
        $this->characteristicType = '';

        session()->flash('type', 'success');
        session()->flash('message', 'Заявка успешно отправлена!');
    }
}