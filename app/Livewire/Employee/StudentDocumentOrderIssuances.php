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

    # Пока один вариант
    # Если будут другие и выборка действительно будет - ${signer_position} и ${signer_fio} в шаблон
    public $listOfSigners = [
        #'Проректор по УиНР' => 'Е.В. Шпилевская',
        'Академический директор' => 'Вартанян Н.А.',
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
        public bool $hadAcademicLeave = false;
        public string $leaveOrders = '';

    private const NOTE_LEAVE = 'Академотпуск';
    private const NOTE_EXPELLED = 'Отчисленные';
    private const NOTE_NOT_ENROLLED = 'Незачисленные';
 
    private function detectLeaveInfo(int $userId): array
    {
        $rows = DB::table('order_user')
            ->join('orders', 'order_user.order_id', '=', 'orders.id')
            ->where('order_user.user_id', '=', $userId)
            ->whereNull('order_user.deleted_at')
            ->whereNull('orders.deleted_at')
            ->orderBy('orders.order_date')
            ->orderBy('order_user.id')
            ->select('order_user.notes', 'orders.order_number', 'orders.order_date')
            ->get();
 
        $format = fn($row) => 'от ' . Carbon::parse($row->order_date)->format('d.m.Y') . ' № ' . $row->order_number . ';';
 
        $lines = [];
        $hadLeave = false;
        $onLeave = false;
        $expelIndex = null;
 
        foreach ($rows as $row) {
            $note = trim((string) $row->notes);
 
            if ($note === self::NOTE_LEAVE) {
                $lines[] = 'Приказ о предоставлении академического отпуска ' . $format($row);
                $hadLeave = true;
                $onLeave = true;
            } elseif ($note === self::NOTE_EXPELLED) {
                $expelIndex = count($lines);
                $lines[] = 'Приказ об отчислении ' . $format($row);
                $onLeave = false;
            } elseif ($note !== '' && $note !== self::NOTE_NOT_ENROLLED) {
                // обычный приказ с группой
                if ($onLeave) {
                    $lines[] = 'Приказ о выходе из академического отпуска ' . $format($row);
                    $onLeave = false;
                }
 
                // после отчисления зачислили снова: строку об отчислении убираем
                if ($expelIndex !== null) {
                    unset($lines[$expelIndex]);
                    $lines = array_values($lines);
                    $expelIndex = null;
                }
            }
        }
 
        if (!$hadLeave) {
            $leaveOrder = DB::table('students_academic_leaves as sal')
                ->leftJoin('orders', 'sal.order_id', '=', 'orders.id')
                ->where('sal.user_id', '=', $userId)
                ->whereNull('sal.deleted_at')
                ->orderBy('sal.id')
                ->select('orders.order_number', 'orders.order_date')
                ->first();
 
            if ($leaveOrder !== null) {
                $hadLeave = true;
 
                if ($leaveOrder->order_number) {
                    array_unshift($lines, 'Приказ о предоставлении академического отпуска ' . $format($leaveOrder));
                }
            }
        }
 
        return ['hadLeave' => $hadLeave, 'lines' => $lines];
    }
    
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
                DB::raw("(SELECT group_name FROM group_student INNER JOIN `groups` ON group_student.group_id = groups.id WHERE group_student.user_id = student_document_order_requests.requester_id ORDER BY group_student.id DESC LIMIT 1) as group_name"),
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

    private function nextDocumentNumber(): int
    {
        $max = StudentDocumentOrderIssuance::whereNotNull('confirmiter_id')
            // ->whereYear('updated_at', now()->year) // Обновление номера справок каждый год
            ->lockForUpdate()
            ->selectRaw('MAX(CAST(document_number AS UNSIGNED)) as max_number')
            ->value('max_number');

        return ((int) $max) + 1;
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
                    'confirmiter_id',
                    'had_academic_leave',
                    'leave_orders'
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

            // $lastConfirmetedDocumentNumber = StudentDocumentOrderIssuance::join('student_document_order_requests', 'student_document_order_issuances.student_document_order_request_id', '=', 'student_document_order_requests.id')
            //     ->where('student_document_order_requests.document_type', '=', $this->documentType)->where('student_document_order_requests.status', '=', 2)
            //     ->select('student_document_order_issuances.document_number')
            //     ->orderBy('student_document_order_issuances.id', 'desc')
            //     ->first();

            // if (is_null($studentDocumentOrderIssuance->confirmiter_id)) {
            //     $this->documentNumber = (is_null($lastConfirmetedDocumentNumber)) ?
            //         StudentDocumentOrderRequest::where('document_type', '=', $this->documentType)->where('status', '=', 2)->count() + 1 :
            //         ++$lastConfirmetedDocumentNumber->document_number;
            // } else {
            //     $this->documentNumber = $lastConfirmetedDocumentNumber->document_number;
            // }

            // Номер только для показа: реально он присваивается в update()
            $this->documentNumber = is_null($studentDocumentOrderIssuance->confirmiter_id)
                ? $this->nextDocumentNumber()
                : (int) $studentDocumentOrderIssuance->document_number;

            $this->checkInDate = (is_null($studentDocumentOrderIssuance->check_in_date)) ? '' : $studentDocumentOrderIssuance->check_in_date;
            $this->checkOutDate = (is_null($studentDocumentOrderIssuance->check_out_date)) ? '' : $studentDocumentOrderIssuance->check_out_date;
            $this->checkInDuration = $studentDocumentOrderIssuance->check_in_duration;
            $this->employmentPlace = $studentDocumentOrderIssuance->employment_place;
            $this->isBudget = $studentDocumentOrderIssuance->is_budget;
            // Подписант по умолчанию: первый из списка
            $this->signer = array_key_first($this->listOfSigners) . '|' . reset($this->listOfSigners);
            if (is_null($studentDocumentOrderIssuance->confirmiter_id)) {
                // Ещё не подтверждена: подставляем подсказку по данным БД
                $leaveInfo = $this->detectLeaveInfo((int) $requesterId);
                $this->hadAcademicLeave = $leaveInfo['hadLeave'];
                $this->leaveOrders = implode("\n", $leaveInfo['lines']);
            } else {
                $this->hadAcademicLeave = (bool) $studentDocumentOrderIssuance->had_academic_leave;
                $this->leaveOrders = (string) $studentDocumentOrderIssuance->leave_orders;
            }
            $this->isShowModalDialog = true;
        } catch (Exception $e) {
            $this->dispatch('show-toast', message: 'Ошибка получения данных из БД!', type: 'error');
        }
    }

    public function update()
    {
        $rules = [
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
            'signer' => 'required',
        ];
 
        if ($this->documentType == 3) {
            $rules += [
                'checkInDate' => 'required',
                'checkOutDate' => 'required',
                'checkInDuration' => 'required|numeric|min:1',
                'employmentPlace' => 'required|string|max:1000',
            ];
        }
 
        $this->validate($rules);
 
        $isConfirmed = DB::transaction(function () {
            $issuance = StudentDocumentOrderIssuance::lockForUpdate()->findOrFail($this->id);
 
            // Уже подтверждена (например, двойной клик): ничего не меняем
            if (!is_null($issuance->confirmiter_id)) {
                return false;
            }
 
            $issuance->document_number = $this->nextDocumentNumber();
            $issuance->last_name = $this->lastName;
            $issuance->first_name = $this->firstName;
            $issuance->middle_name = $this->middleName;
            $issuance->last_name_genitive = $this->lastNameGenitive;
            $issuance->first_name_genitive = $this->firstNameGenitive;
            $issuance->middle_name_genitive = $this->middleNameGenitive;
            $issuance->last_name_dative = $this->lastNameDative;
            $issuance->first_name_dative = $this->firstNameDative;
            $issuance->middle_name_dative = $this->middleNameDative;
            $issuance->birthday = Carbon::parse($this->birthday)->format('Y-m-d');
            $issuance->sex = ($this->sex == 'муж.') ? 0 : 1;
            $issuance->course = $this->course;
            $issuance->level_education_name = $this->levelEducationName;
            $issuance->teaching_form_name = $this->teachingFormName;
            $issuance->speciality_name = $this->specialityName;
            $issuance->specialty_code = $this->specialtyCode;
            $issuance->orders = $this->order;
            $issuance->training_period = $this->trainingPeriod;
 
            if ($this->documentType == 3) {
                $issuance->check_in_date = Carbon::parse($this->checkInDate)->format('Y-m-d');
                $issuance->check_out_date = Carbon::parse($this->checkOutDate)->format('Y-m-d');
                $issuance->check_in_duration = $this->checkInDuration;
                $issuance->employment_place = $this->employmentPlace;
            }
 
            $issuance->had_academic_leave = $this->hadAcademicLeave;
            $issuance->leave_orders = trim($this->leaveOrders) !== '' ? trim($this->leaveOrders) : null;
            $issuance->is_budget = $this->isBudget;
            $issuance->confirmiter_id = Auth::user()->id;
 
            $signerData = explode('|', $this->signer);
            $issuance->signer_position = $signerData[0];
            $issuance->signer_fio = $signerData[1] ?? '';
 
            $issuance->update();
 
            $request = StudentDocumentOrderRequest::findOrFail($this->studentDocumentOrderRequestId);
            $request->status = 2;
            $request->update();
 
            return true;
        });
 
        $this->isShowModalDialog = false;
 
        if ($isConfirmed) {
            $this->dispatch('show-toast', message: 'Заявка успешно подтверждена!', type: 'success');
        } else {
            $this->dispatch('show-toast', message: 'Заявка уже была подтверждена ранее', type: 'info');
        }
    }

    public function downloadStudentDocument($studentDocumentOrderRequestId)
    {
        try {
            $data = (new StudentDocumentOrderReportController())->downloadStudentDocument($studentDocumentOrderRequestId);
        } catch (\DomainException $e) {
            $this->dispatch('show-toast', message: $e->getMessage(), type: 'error');
            return;
        }

        return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
    }
}
