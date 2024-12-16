<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Report\StudentDocumentOrderReportController;
use App\Models\GroupStudent;
use App\Models\StudentDocumentOrderIssuance;
use App\Models\StudentDocumentOrderRequest;
use App\Models\UserData;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Livewire\Component;
use Livewire\WithPagination;

class StudentDocumentOrderIssuances extends Component
{
    use WithPagination;

    public bool $isShowModalDialog = false;

    public int $selectedDocumentType = 0;
    public int $selectedStatus = 0;
    public string $signer = '';

    public $listOfSigners = [
        'Проректор по УиНР' => 'Е.В. Шпилевская',
        'Академический директор' => 'Д.А. Лимарева',
    ];

    public $listOfDocumentTypes = [
        1 => 'Справка о статусе студента',
        2 => 'Справка о статусе студента (для транспортных карт) - только для студентов колледжа',
        3 => 'Справка-вызов только для студентов очно-заочной и заочной форм обучения',
        4 => 'Характеристика'
    ];

    public $listOfStatuses = [
        1 => 'В работе',
        2 => 'Выполнена',
        3 => 'Отклонена'
    ];

    public $id = 0,
        $studentDocumentOrderRequestId = 0,
        $lastName = '',
        $firstName = '',
        $middleName = '',
        $lastNameGenitive = '',
        $firstNameGenitive = '',
        $middleNameGenitive = '',
        $lastNameDative = '',
        $firstNameDative = '',
        $middleNameDative = '',
        $birthday = '',
        $sex = '',
        $course = 0,
        $levelEducationName = '',
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

    public function render()
    {
        $studentDocumentOrderIssuances = $this->getStudentDocumentOrderIssuance();

        return view('livewire.employee.student-document-order-issuances', [
            'studentDocumentOrderIssuances' => $studentDocumentOrderIssuances
        ]);
    }

    public function setDocumentType()
    {
        $this->selectedDocumentType = $this->selectedDocumentType;
    }

    public function setStatus()
    {
        $this->selectedStatus = $this->selectedStatus;
    }

    public function getStudentDocumentOrderIssuance()
    {
        $query = StudentDocumentOrderIssuance::join('student_document_order_requests', 'student_document_order_issuances.student_document_order_request_id', '=', 'student_document_order_requests.id')
            ->join('users_data', 'student_document_order_requests.requester_id', '=', 'users_data.user_id')
            ->select(
                'student_document_order_issuances.id',
                'student_document_order_issuances.student_document_order_request_id',
                'student_document_order_requests.document_type',
                'student_document_order_requests.status',
                'student_document_order_requests.requester_id',
                'student_document_order_requests.academic_year',
                'student_document_order_requests.half_year',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("(SELECT group_name FROM group_student INNER JOIN groups ON group_student.group_id = groups.id WHERE group_student.user_id = student_document_order_requests.requester_id ORDER BY group_student.id DESC LIMIT 1) as group_name"),
                'student_document_order_issuances.confirmiter_id',
                'student_document_order_requests.created_at'
            );

        if ($this->selectedDocumentType != 0) {
            $query->where('student_document_order_requests.document_type', '=', $this->selectedDocumentType);
        }

        if ($this->selectedStatus != 0) {
            $query->where('student_document_order_requests.status', '=', $this->selectedStatus);
        }

        return $query->paginate(50);
    }

    public function edit($studentDocumentOrderRequestId, $requesterId, $documentType)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        try {
            $this->studentDocumentOrderRequestId = $studentDocumentOrderRequestId;
            $this->documentType = $documentType;

            $userData = UserData::where('user_id', '=', $requesterId)
                ->select(
                    'last_name',
                    'first_name',
                    'middle_name',
                    'last_name_genitive',
                    'first_name_genitive',
                    'middle_name_genitive',
                    'last_name_dative',
                    'first_name_dative',
                    'middle_name_dative',
                    'birthday',
                    'sex'
                )
                ->first();

            $groupStudent = GroupStudent::join('groups', 'group_student.group_id', '=', 'groups.id')
                ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
                ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
                ->join('specialities',  'work_study_plans.speciality_id', '=', 'specialities.id')
                ->join('level_educations',  'specialities.level_education_id', '=', 'level_educations.id')
                ->where('group_student.user_id', '=', $requesterId)
                ->select(
                    'groups.group_name',
                    'groups.course',
                    'groups.year_admission',
                    'groups.year_completion',
                    'teaching_forms.teaching_form_name',
                    'specialities.specialty_code',
                    'specialities.speciality_name',
                    'level_educations.level_name'
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

            $studentDocumentOrderIssuance = StudentDocumentOrderIssuance::where('student_document_order_request_id', '=', $studentDocumentOrderRequestId)
                ->select(
                    'id',
                    'document_number',
                    'check_in_date',
                    'check_out_date',
                    'check_in_duration',
                    'employment_place',
                    'is_budget',
                    'confirmiter_id'
                )
                ->first();

            $this->id = $studentDocumentOrderIssuance->id;
            $this->lastName = $userData->last_name;
            $this->firstName = $userData->first_name;
            $this->middleName = $userData->middle_name;
            $this->lastNameGenitive = $userData->last_name_genitive;
            $this->firstNameGenitive = $userData->first_name_genitive;
            $this->middleNameGenitive = $userData->middle_name_genitive;
            $this->lastNameDative = $userData->last_name_dative;
            $this->firstNameDative = $userData->first_name_dative;
            $this->middleNameDative = $userData->middle_name_dative;
            $this->birthday = Carbon::parse($userData->birthday)->format('Y-m-d');
            $this->sex = ($userData->sex) ? 'жен.' : 'муж.';
            $this->course = $groupStudent->course;
            $this->levelEducationName = $groupStudent->level_name;
            $this->teachingFormName = $groupStudent->teaching_form_name;
            $this->specialtyCode = $groupStudent->specialty_code;
            $this->specialityName = $groupStudent->speciality_name;

            $this->order = Carbon::parse($orderUser->order_date)->format('d.m.Y') . ' № ' . $orderUser->order_number;
            $this->trainingPeriod = 'с ' . Carbon::parse($groupStudent->year_admission)->format('d.m.Y') . 'г. по ' . Carbon::parse($groupStudent->year_completion)->format('d.m.Y') . 'г.';

            $lastConfirmetedDocumentNumber = StudentDocumentOrderIssuance::join('student_document_order_requests', 'student_document_order_issuances.student_document_order_request_id', '=', 'student_document_order_requests.id')
                ->where('student_document_order_requests.document_type', '=', $this->documentType)->where('student_document_order_requests.status', '=', 2)
                ->select('student_document_order_issuances.document_number')
                ->orderBy('student_document_order_issuances.id', 'desc')
                ->first();

            if (is_null($studentDocumentOrderIssuance->confirmiter_id)) {
                $this->documentNumber = (is_null($lastConfirmetedDocumentNumber)) ?
                    StudentDocumentOrderRequest::where('document_type', '=', $this->documentType)->where('status', '=', 2)->count() + 1 :
                    ++$lastConfirmetedDocumentNumber->document_number;
            } else {
                $this->documentNumber = $lastConfirmetedDocumentNumber->document_number;
            }

            $this->checkInDate = (is_null($studentDocumentOrderIssuance->check_in_date)) ? '' : $studentDocumentOrderIssuance->check_in_date;
            $this->checkOutDate = (is_null($studentDocumentOrderIssuance->check_out_date)) ? '' : $studentDocumentOrderIssuance->check_out_date;
            $this->checkInDuration = $studentDocumentOrderIssuance->check_in_duration;
            $this->employmentPlace = $studentDocumentOrderIssuance->employment_place;
            $this->isBudget = $studentDocumentOrderIssuance->is_budget;

            $this->isShowModalDialog = true;
        } catch (Exception $e) {
            $this->dispatch('show-toast', message: 'Ошибка получения данных из БД!', type: 'error');
        }
    }

    public function update()
    {
        if ($this->documentType != 3) {
            $this->validate([
                'documentNumber' => 'required|numeric|min:1',
                'lastName' => 'required|string|max:50',
                'firstName' => 'required|string|max:50',
                'middleName' => 'required|string|max:50',
                'lastNameGenitive' => 'required|string|max:50',
                'firstNameGenitive' => 'required|string|max:50',
                'middleNameGenitive' => 'required|string|max:50',
                'lastNameDative' => 'required|string|max:50',
                'firstNameDative' => 'required|string|max:50',
                'middleNameDative' => 'required|string|max:50',
                'birthday' => 'required',
                'course' => 'required|numeric|min:1',
                'teachingFormName' => 'required|string|max:255',
                'specialityName' => 'required|string|max:255',
                'specialtyCode' => 'required|string|max:255',
                'order' => 'required|string|max:255',
                'trainingPeriod' => 'required|string|max:255',
                'signer' => 'required'
            ]);
        } else {
            $this->validate([
                'documentNumber' => 'required|numeric|min:1',
                'lastName' => 'required|string|max:50',
                'firstName' => 'required|string|max:50',
                'middleName' => 'required|string|max:50',
                'lastNameGenitive' => 'required|string|max:50',
                'firstNameGenitive' => 'required|string|max:50',
                'middleNameGenitive' => 'required|string|max:50',
                'lastNameDative' => 'required|string|max:50',
                'firstNameDative' => 'required|string|max:50',
                'middleNameDative' => 'required|string|max:50',
                'birthday' => 'required',
                'course' => 'required|numeric|min:1',
                'teachingFormName' => 'required|string|max:255',
                'specialityName' => 'required|string|max:255',
                'specialtyCode' => 'required|string|max:255',
                'order' => 'required|string|max:255',
                'trainingPeriod' => 'required|string|max:255',
                'checkInDate' => 'required',
                'checkOutDate' => 'required',
                'checkInDuration' => 'required|numeric|min:1',
                'employmentPlace' => 'required|string|max:1000',
                'signer' => 'required'
            ]);
        }

        $studentDocumentOrderIssuance = StudentDocumentOrderIssuance::findOrFail($this->id);
        $studentDocumentOrderIssuance->document_number = $this->documentNumber;
        $studentDocumentOrderIssuance->last_name = $this->lastName;
        $studentDocumentOrderIssuance->first_name = $this->firstName;
        $studentDocumentOrderIssuance->middle_name = $this->middleName;
        $studentDocumentOrderIssuance->last_name_genitive = $this->lastNameGenitive;
        $studentDocumentOrderIssuance->first_name_genitive = $this->firstNameGenitive;
        $studentDocumentOrderIssuance->middle_name_genitive = $this->middleNameGenitive;
        $studentDocumentOrderIssuance->last_name_dative = $this->lastNameDative;
        $studentDocumentOrderIssuance->first_name_dative = $this->firstNameDative;
        $studentDocumentOrderIssuance->middle_name_dative = $this->middleNameDative;
        $studentDocumentOrderIssuance->birthday = Carbon::parse($this->birthday)->format('Y-m-d');
        $studentDocumentOrderIssuance->sex = ($this->sex == 'муж.') ? 0 : 1;
        $studentDocumentOrderIssuance->course = $this->course;
        $studentDocumentOrderIssuance->level_education_name = $this->levelEducationName;
        $studentDocumentOrderIssuance->teaching_form_name = $this->teachingFormName;
        $studentDocumentOrderIssuance->speciality_name = $this->specialityName;
        $studentDocumentOrderIssuance->specialty_code = $this->specialtyCode;
        $studentDocumentOrderIssuance->orders = $this->order;
        $studentDocumentOrderIssuance->training_period = $this->trainingPeriod;

        if ($this->documentType == 3) {
            $studentDocumentOrderIssuance->check_in_date = Carbon::parse($this->checkInDate)->format('Y-m-d');
            $studentDocumentOrderIssuance->check_out_date = Carbon::parse($this->checkOutDate)->format('Y-m-d');
            $studentDocumentOrderIssuance->check_in_duration = $this->checkInDuration;
            $studentDocumentOrderIssuance->employment_place = $this->employmentPlace;
        }

        $studentDocumentOrderIssuance->is_budget = $this->isBudget;
        $studentDocumentOrderIssuance->confirmiter_id = Auth::user()->id;

        $signerData = explode('|', $this->signer);
        $studentDocumentOrderIssuance->signer_position = $signerData[0];
        $studentDocumentOrderIssuance->signer_fio = $signerData[1];

        $studentDocumentOrderIssuance->update();

        $studentDocumentOrderRequest = StudentDocumentOrderRequest::findOrFail($this->studentDocumentOrderRequestId);
        $studentDocumentOrderRequest->status = 2;
        $studentDocumentOrderRequest->update();

        $this->isShowModalDialog = false;

        $this->dispatch('show-toast', message: 'Заявка успешно подтверждена!', type: 'success');
    }

    public function downloadStudentDocument($studentDocumentOrderRequestId)
    {
        $studentDocumentOrderReportController = new StudentDocumentOrderReportController();
        $data = $studentDocumentOrderReportController->downloadStudentDocument($studentDocumentOrderRequestId);

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
    }
}
