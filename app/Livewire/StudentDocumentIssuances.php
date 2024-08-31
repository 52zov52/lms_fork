<?php

namespace App\Livewire;

use App\Models\GroupStudent;
use App\Models\StudentDocumentIssuance;
use App\Models\StudentDocumentRequest;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StudentDocumentIssuances extends Component
{
    public $id = 0,
        $studentDocumentRequestId = 0,
        $lastName = '',
        $firstName = '',
        $middleName = '',
        $lastNameDative = '',
        $firstNameDative = '',
        $middleNameDative = '',
        $sex = '',
        $course = 0,
        $teachingFormName = '',
        $specialityName = '',
        $specialtyCode = '',
        $order = '',
        $trainingPeriod = '',
        $documentNumber = 0,
        $documentType = 0,
        $checkInDate = '',
        $checkOutDate = '',
        $checkInDuration = 0,
        $employmentPlace = '',
        $isBudget = false;

    public $showModal = false;

    public $listDocumentTypes = [
        1 => 'Справка о статусе студента',
        2 => 'Справка о статусе студента (для транспортных карт) - только для студентов колледжа',
        3 => 'Справка-вызов только для студентов очно-заочной и заочной форм обучения'
    ];

    public $selectedDocumentType = 0;

    public function render()
    {
        $query = StudentDocumentIssuance::join('student_document_requests', 'student_document_issuances.student_document_request_id', '=', 'student_document_requests.id')
            ->join('users_data', 'student_document_requests.requester_id', '=', 'users_data.user_id')
            ->where('student_document_requests.status', '=', 1)
            ->select(
                'student_document_issuances.id',
                'student_document_issuances.student_document_request_id',
                'student_document_requests.document_type',
                'student_document_requests.status',
                'student_document_requests.requester_id',
                'student_document_requests.academic_year',
                'student_document_requests.half_year',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("(SELECT group_name FROM group_student INNER JOIN groups ON group_student.group_id = groups.id WHERE group_student.user_id = student_document_requests.requester_id ORDER BY group_student.id DESC LIMIT 1) as group_name"),
                'student_document_issuances.confirmiter_id',
                'student_document_requests.created_at'
            );

        if ($this->selectedDocumentType != 0) {
            $query->where('student_document_requests.document_type', '=', $this->selectedDocumentType);
        }

        $studentDocumentIssuances = $query->paginate(50);

        return view('livewire.student-document-issuances', [
            'listDocumentTypes' => $this->listDocumentTypes,
            'studentDocumentIssuances' => $studentDocumentIssuances
        ]);
    }

    public function callMethod($methodname)
    {
        $this->{$methodname}();
    }

    public function setDocumentType()
    {
        $this->selectedDocumentType = $this->selectedDocumentType;
    }

    public function edit($studentDocumentRequestId, $requesterId, $documentType)
    {
        $this->resetValidation();
        $this->resetErrorBag();

        $this->studentDocumentRequestId = $studentDocumentRequestId;
        $this->documentType = $documentType;

        $userData = UserData::where('user_id', '=', $requesterId)
            ->select(
                'last_name',
                'first_name',
                'middle_name',
                'last_name_dative',
                'first_name_dative',
                'middle_name_dative',
                'sex'
            )
            ->first();

        $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
            ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
            ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
            ->join('specialities',  'work_study_plans.speciality_id', '=', 'specialities.id')
            ->where('group_student.user_id', '=', $requesterId)
            ->select(
                'groups.group_name',
                'groups.course',
                'groups.year_admission',
                'groups.year_completion',
                'teaching_forms.teaching_form_name',
                'specialities.specialty_code',
                'specialities.speciality_name',
                'specialities.level_education_id'
            )
            ->orderBy('group_student.id', 'desc')
            ->first();

        $orderUser = DB::table('order_user')
            ->join('orders', 'order_user.order_id', '=', 'orders.id')
            ->where('order_user.user_id', '=', $requesterId)
            ->select(
                'order_user.id',
                'orders.order_number',
                'orders.order_date',
                'orders.order_type_id'
            )
            ->first();

        $studentDocumentIssuance = StudentDocumentIssuance::where('student_document_request_id', '=', $studentDocumentRequestId)
            ->select(
                'id',
                'document_number',
                'check_in_date',
                'check_out_date',
                'check_in_duration',
                'employment_place',
                'is_budget'
            )
            ->first();

        $this->lastName = $userData->last_name;
        $this->firstName = $userData->first_name;
        $this->middleName = $userData->middle_name;
        $this->lastNameDative = $userData->last_name_dative;
        $this->firstNameDative = $userData->first_name_dative;
        $this->middleNameDative = $userData->middle_name_dative;
        $this->sex = ($userData->sex) ? 'жен.' : 'муж.';
        $this->course = $groupStudent->course;
        $this->teachingFormName = $groupStudent->teaching_form_name;
        $this->specialtyCode = $groupStudent->specialty_code;
        $this->specialityName = $groupStudent->speciality_name;

        $this->order = Carbon::parse($orderUser->order_date)->format('d.m.Y') . ' № ' . $orderUser->order_number;
        $this->trainingPeriod = 'с ' . Carbon::parse($groupStudent->year_admission)->format('d.m.Y') . 'г. по ' . Carbon::parse($groupStudent->year_completion)->format('d.m.Y') . 'г.';

        $this->id = $studentDocumentIssuance->id;
        $this->documentNumber = StudentDocumentRequest::where('document_type', '=', $this->documentType)->where('status', '=', 2)->count() + 1;
        $this->checkInDate = (is_null($studentDocumentIssuance->check_in_date)) ? '' : Carbon::parse($studentDocumentIssuance->check_in_date)->format('d.m.Y');
        $this->checkOutDate = (is_null($studentDocumentIssuance->check_out_date)) ? '' : Carbon::parse($studentDocumentIssuance->check_out_date)->format('d.m.Y');
        $this->checkInDuration = $studentDocumentIssuance->check_in_duration;
        $this->employmentPlace = $studentDocumentIssuance->employment_place;
        $this->isBudget = $studentDocumentIssuance->is_budget;

        $this->showModal = true;
    }

    public function update()
    {
        if ($this->documentType != 3) {
            $this->validate([
                'documentNumber' => 'required|numeric|min:1',
                'lastNameDative' => 'required|string|max:50',
                'firstNameDative' => 'required|string|max:50',
                'middleNameDative' => 'required|string|max:50',
                'course' => 'required|numeric|min:1',
                'teachingFormName' => 'required|string|max:255',
                'specialityName' => 'required|string|max:255',
                'specialtyCode' => 'required|string|max:255',
                'order' => 'required|string|max:255',
                'trainingPeriod' => 'required|string|max:255',
            ]);
        } else {
            $this->validate([
                'documentNumber' => 'required|numeric|min:1',
                'lastName' => 'required|string|max:50',
                'firstName' => 'required|string|max:50',
                'middleName' => 'required|string|max:50',
                'lastNameDative' => 'required|string|max:50',
                'firstNameDative' => 'required|string|max:50',
                'middleNameDative' => 'required|string|max:50',
                'course' => 'required|numeric|min:1',
                'teachingFormName' => 'required|string|max:255',
                'specialityName' => 'required|string|max:255',
                'specialtyCode' => 'required|string|max:255',
                'order' => 'required|string|max:255',
                'trainingPeriod' => 'required|string|max:255',
                'checkInDate' => 'required',
                'checkOutDate' => 'required',
                'checkInDuration' => 'required|numeric|min:1',
                'employmentPlace' => 'required|string|max:1000'
            ]);
        }

        $studentDocumentIssuance = StudentDocumentIssuance::findOrFail($this->id);
        $studentDocumentIssuance->document_number = $this->documentNumber;
        $studentDocumentIssuance->last_name = $this->lastName;
        $studentDocumentIssuance->first_name = $this->firstName;
        $studentDocumentIssuance->middle_name = $this->middleName;
        $studentDocumentIssuance->last_name_dative = $this->lastNameDative;
        $studentDocumentIssuance->first_name_dative = $this->firstNameDative;
        $studentDocumentIssuance->middle_name_dative = $this->middleNameDative;
        $studentDocumentIssuance->sex = ($this->sex == 'муж.') ? 0 : 1;
        $studentDocumentIssuance->course = $this->course;
        $studentDocumentIssuance->teaching_form_name = $this->teachingFormName;
        $studentDocumentIssuance->speciality_name = $this->specialityName;
        $studentDocumentIssuance->specialty_code = $this->specialtyCode;
        $studentDocumentIssuance->orders = $this->order;
        $studentDocumentIssuance->training_period = $this->trainingPeriod;

        if ($this->documentType == 3) {
            $studentDocumentIssuance->check_in_date = Carbon::parse($this->checkInDate)->format('Y-m-d');
            $studentDocumentIssuance->check_out_date = Carbon::parse($this->checkOutDate)->format('Y-m-d');
            $studentDocumentIssuance->check_in_duration = $this->checkInDuration;
            $studentDocumentIssuance->employment_place = $this->employmentPlace;
        }

        $studentDocumentIssuance->is_budget = $this->isBudget;
        $studentDocumentIssuance->confirmiter_id = Auth::user()->id;
        $studentDocumentIssuance->update();

        $studentDocumentRequest = StudentDocumentRequest::findOrFail($this->studentDocumentRequestId);
        $studentDocumentRequest->status = 2;
        $studentDocumentRequest->update();

        $this->showModal = false;

        session()->flash('type', 'success');
        session()->flash('message', 'Заявка успешно подтверждена!');
    }

    public function print($studentDocumentRequestId)
    {
        return redirect()->route('print.document', ['id' => $studentDocumentRequestId]);
    }
}
