<?php

namespace App\Livewire;

use App\Models\ConfirmedDocumentRequest;
use App\Models\DocumentOrderRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentRetrievalRequest extends Component
{
    use WithPagination;

    public bool $isDocumentOrderRequestModalVisible = false, $isBudget = false, $isAlertVisible = false, $isConfirmDocumentIssuanceModalVisible = false;
    public string $lastName = "", $firstName = "", $middleName = "", $sex = "", $teachingFormName = "", $specialtyCode = "", $specialityName = "", $trainingPeriod = "", $orders = "";
    public int $course = 0, $documentOrderRequestId = 0;

    public function render()
    {
        $documentOrderRequest = DocumentOrderRequest::join('users_data', 'document_order_requests.requester_id', '=', 'users_data.user_id')
            ->join('group_student', 'document_order_requests.requester_id', '=', 'group_student.user_id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->select(
                'document_order_requests.id',
                'document_order_requests.document_type',
                'document_order_requests.status',
                'document_order_requests.requester_id',
                'document_order_requests.request_date',
                'document_order_requests.confirmed_date',
                'document_order_requests.issue_date',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'groups.group_name'
            )
            ->paginate(25);

        return view('livewire.document-retrieval-request', [
            'documentOrderRequest' => $documentOrderRequest
        ]);
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function confirmDocumentIssuanceModalVisible($documentOrderRequestId): void
    {
        $this->documentOrderRequestId = $documentOrderRequestId;
        $this->isConfirmDocumentIssuanceModalVisible = true;
    }

    public function edit($documentOrderRequestId, $userId): void
    {
        $this->documentOrderRequestId = $documentOrderRequestId;

        $confirmedDocumentRequest = ConfirmedDocumentRequest::where('document_order_request_id', '=', $this->documentOrderRequestId)->first();

        if (is_null($confirmedDocumentRequest)) {
            $this->orders = '';

            $userInfo = DB::table('user_status')
                ->select(
                    'user_status.user_id',
                    'users_data.last_name_dative',
                    'users_data.first_name_dative',
                    'users_data.middle_name_dative',
                    'users_data.sex',
                    'groups.group_name',
                    'groups.course',
                    'teaching_forms.id as teaching_form_id',
                    'teaching_forms.teaching_form_name',
                    'specialities.specialty_code',
                    'specialities.speciality_name',
                    'specialities.level_education_id',
                    'work_study_plans.approval_date',
                    'study_plans.id as study_plan_id',
                    'study_plans.number_years'
                )
                ->join('users_data', 'user_status.user_id', '=', 'users_data.user_id')
                ->join('group_student', 'user_status.user_id', '=', 'group_student.user_id')
                ->join('groups', 'group_student.group_id', '=', 'groups.id')
                ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
                ->join('study_plans', 'groups.study_plan_id', '=', 'study_plans.id')
                ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
                ->join('specialities',  'work_study_plans.speciality_id', '=', 'specialities.id')
                ->where('user_status.status_id', '=', 3)
                ->where('user_status.user_id', '=', $userId)->first();

            $ordersInfo = DB::table('order_user')
                ->select(
                    'order_user.id',
                    'orders.order_number',
                    'orders.order_date',
                    'orders.order_type_id'
                )
                ->join('orders', 'order_user.order_id', '=', 'orders.id')
                ->where('order_user.user_id', '=', $userId)->get();

            $this->lastName = $userInfo->last_name_dative;
            $this->firstName = $userInfo->first_name_dative;
            $this->middleName = $userInfo->middle_name_dative;

            $this->sex = $userInfo->sex;
            $this->course = $userInfo->course;
            $this->teachingFormName = $userInfo->teaching_form_name;

            $this->specialtyCode = $userInfo->specialty_code;
            $this->specialityName = $userInfo->speciality_name;

            foreach ($ordersInfo as $value) {
                $this->orders .= Carbon::parse($value->order_date)->format('d.m.Y') . ' № ' . $value->order_number . ', ';
            }

            $this->orders = substr($this->orders, 0, -2);

            $this->trainingPeriod = 'с 01.09.' . Carbon::parse($userInfo->approval_date)->format('Y') . ' г. по 30.06.' . Carbon::parse($userInfo->approval_date)->addYears($userInfo->number_years)->format('Y') . ' г.';
        } else {
            $this->lastName = $confirmedDocumentRequest->last_name;
            $this->firstName = $confirmedDocumentRequest->first_name;
            $this->middleName = $confirmedDocumentRequest->middle_name;
            $this->sex = $confirmedDocumentRequest->sex;
            $this->course = $confirmedDocumentRequest->course;
            $this->teachingFormName = $confirmedDocumentRequest->teaching_form_name;
            $this->specialtyCode = $confirmedDocumentRequest->specialty_code;
            $this->specialityName = $confirmedDocumentRequest->speciality_name;
            $this->orders = $confirmedDocumentRequest->orders;
            $this->trainingPeriod = $confirmedDocumentRequest->training_period;
            $this->isBudget = $confirmedDocumentRequest->is_budget;
        }

        switch ($this->teachingFormName) {
            case 'Очная':
                $this->teachingFormName = 'очной';
                break;
            case 'Заочная':
                $this->teachingFormName = 'заочной';
                break;
            default:
                $this->teachingFormName = 'очно-заочной';
                break;
        }

        $this->isDocumentOrderRequestModalVisible = true;
    }

    public function store(): void
    {
        $confirmedDocumentRequest = ConfirmedDocumentRequest::where('document_order_request_id', '=', $this->documentOrderRequestId)->first();

        if (is_null($confirmedDocumentRequest)) {

            $this->validate([
                'lastName' => 'required|max:50',
                'firstName' => 'required|max:50',
                'middleName' => 'required|max:50',
                'course' => 'required',
                'teachingFormName' => 'required|max:20',
                'specialtyCode' => 'required|max:20',
                'specialityName' => 'required|max:100',
                'orders' => 'required|max:5000',
                'trainingPeriod' => 'required|max:50'
            ]);

            ConfirmedDocumentRequest::create([
                'last_name' => $this->lastName,
                'first_name' => $this->firstName,
                'middle_name' => $this->middleName,
                'sex' => $this->sex,
                'course' => $this->course,
                'teaching_form_name' => $this->teachingFormName,
                'speciality_name' => $this->specialityName,
                'specialty_code' => $this->specialtyCode,
                'orders' => $this->orders,
                'training_period' => $this->trainingPeriod,
                'is_budget' => $this->isBudget,
                'confirmiter_id' => Auth::user()->id,
                'document_order_request_id' => $this->documentOrderRequestId
            ]);

            $documentOrderRequest = DocumentOrderRequest::findOrFail($this->documentOrderRequestId);
            $documentOrderRequest->document_number = (DocumentOrderRequest::where('status', '=', 2)->orWhere('status', '=', 4)->count() + 1);
            $documentOrderRequest->status = 2;
            $documentOrderRequest->confirmed_date = Carbon::now();
            $documentOrderRequest->update();

            $this->isAlertVisible = true;
            session()->flash('message-success', 'Заявка успешно подтверждена!');
        } else {
            $this->isAlertVisible = true;
            session()->flash('message-error', 'Повторное подтверждение заявки запрещено!');
        }

        $this->isDocumentOrderRequestModalVisible = false;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function printDocument($documentOrderRequestId)
    {
        $confirmedDocumentRequest = ConfirmedDocumentRequest::where('document_order_request_id', '=', $documentOrderRequestId)->first();

        if (is_null($confirmedDocumentRequest)) {
            $this->isAlertVisible = true;
            session()->flash('message-error', 'Перед печатью бланка, данные в заявке должны быть подтверждены!');
        } else {
            $documentOrderRequest = DocumentOrderRequest::findOrFail($documentOrderRequestId);
            if (is_null($documentOrderRequest->print_date)) {
                $documentOrderRequest->print_date = Carbon::now();
                $documentOrderRequest->update();
            }

            return redirect()->route('print.document', ['id' => $documentOrderRequestId]);
        }
    }

    public function confirmDocumentIssuance(): void
    {
        $confirmedDocumentRequest = ConfirmedDocumentRequest::where('document_order_request_id', '=', $this->documentOrderRequestId)->first();

        if (is_null($confirmedDocumentRequest)) {
            $this->isConfirmDocumentIssuanceModalVisible = false;
            $this->isAlertVisible = true;
            session()->flash('message-error', 'Перед подтверждением выдачи документа (справки), должны быть подтверждены данные в заявке!');
        } else {
            $documentOrderRequest = DocumentOrderRequest::findOrFail($this->documentOrderRequestId);
            if(is_null($documentOrderRequest->issue_date)) {
                $documentOrderRequest->status = 4;
                $documentOrderRequest->issuer_id = Auth::user()->id;
                $documentOrderRequest->issue_date = Carbon::now();
                $documentOrderRequest->update();

                $this->isConfirmDocumentIssuanceModalVisible = false;
                $this->isAlertVisible = true;
                session()->flash('message-success', 'Выдача документа (справки) успешно подтверждена!');
            }
            else {
                $this->isConfirmDocumentIssuanceModalVisible = false;
                $this->isAlertVisible = true;
                session()->flash('message-error', 'Повторное подтверждение выдачи документа (справки) запрещено!');
            }
        }
    }
}
