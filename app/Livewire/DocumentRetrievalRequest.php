<?php

namespace App\Livewire;

use App\Models\ConfirmedDocumentRequest;
use App\Models\DocumentOrderRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DocumentRetrievalRequest extends Component
{
    public bool $isDocumentOrderRequestModalVisible = false;
    public string $lastName = "", $firstName = "", $middleName = "", $sex = "", $teachingFormName = "", $specialtyCode = "", $specialityName = "", $trainingPeriod = "", $orders = "";
    public int $course = 0, $documentOrderRequestId = 0;

    public function render()
    {
        $documentOrderRequest = DocumentOrderRequest::join('users_data', 'document_order_requests.requester_id', '=', 'users_data.user_id')
            ->join('group_student', 'document_order_requests.requester_id', '=', 'group_student.user_id')
            ->join('groups', 'group_student.group_id', '=', 'groups.id')
            ->select(
                'document_order_requests.id',
                'document_order_requests.id',
                'document_order_requests.document_type',
                'document_order_requests.status',
                'document_order_requests.requester_id',
                'document_order_requests.request_date',
                'document_order_requests.issue_date',
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name',
                'groups.group_name'
            )
            ->get();

        return view('livewire.document-retrieval-request', [
            'documentOrderRequest' => $documentOrderRequest
        ]);
    }

    public function documentOrderRequestModalVisible($documentOrderRequestId, $userId): void
    {
        $this->documentOrderRequestId = $documentOrderRequestId;

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
            $this->orders .= 'Приказы – основания: от ' . Carbon::parse($value->order_date)->format('d.m.Y') . ' № ' . $value->order_number . ', ';
        }

        $this->orders = substr($this->orders, 0, -2);

        $this->trainingPeriod = 'с 01.09.' . Carbon::parse($userInfo->approval_date)->format('Y') . ' г. по 30.06.' . Carbon::parse($userInfo->approval_date)->addYears($userInfo->number_years)->format('Y') . ' г.';

        $this->isDocumentOrderRequestModalVisible = true;
    }

    public function checkRequest()
    {
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
            'document_order_request_id' => $this->documentOrderRequestId
        ]);

        $documentOrderRequest = DocumentOrderRequest::findOrFail($this->documentOrderRequestId);
        $documentOrderRequest->document_number = (DocumentOrderRequest::where('status', '=', 2)->count() + 1);
        $documentOrderRequest->status = 2;
        $documentOrderRequest->issuer_id = Auth::user()->id;
        $documentOrderRequest->issue_date = Carbon::now();
        $documentOrderRequest->update();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function printDocument($documentOrderRequestId)
    {
        return redirect()->route('print.document', ['id' => $documentOrderRequestId]);
    }
}
