<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\Report\StudentDocumentOrderReportController;
use App\Models\GroupStudent;
use App\Models\OpopLeader;
use App\Models\StudentDocumentOrderIssuance;
use App\Models\StudentDocumentOrderRequest;
use App\Models\UserData;
use App\Support\StudentOrderHistory;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class StudentDocumentOrderIssuances extends Component
{
    use WithPagination;

    public bool $isShowModalDialog = false;
    public $quantity = 1;
    public int $selectedDocumentType = 0;
    public int $selectedStatus = 0;
    public string $signer = '';

    // Подписанты: академический директор всегда; руководители ОПОП — только для характеристики (тип 4).
    // Значение в select: «должность|ФИО» -> в шаблон ${signer_position} и ${signer_fio}
    private const DIRECTOR_POSITION = 'Академический директор';
    private const DIRECTOR_FIO = 'Вартанян Н.А.';
    public const OPOP_POSITION = 'Руководитель ОПОП';

    public string $newOpopFio = '';

    #[Computed]
    public function signers(): array
    {
        $list = [
            self::DIRECTOR_POSITION . '|' . self::DIRECTOR_FIO
                => self::DIRECTOR_POSITION . ' (' . self::DIRECTOR_FIO . ')',
        ];

        if ((int) $this->documentType === 4) {
            // Фиксированный подписант — проректор по воспитательной работе и молодёжной политике
            $list['Проректор по ВР и МП|Фоменко А.И.']
                = 'Проректор по ВР и МП (Фоменко А.И.)';

            foreach (OpopLeader::where('is_active', true)->orderBy('fio')->get() as $leader) {
                $short = $leader->short_fio;
                $list[self::OPOP_POSITION . '|' . $short]
                    = self::OPOP_POSITION . ' (' . $short . ')';
            }
        }

        return $list;
    }

    // Метод оставлен, но UI добавления ОПОП убран из blade.
    public function addOpopLeader()
    {
        if ((int) $this->documentType !== 4) {
            return;
        }

        $fio = trim(preg_replace('/\s+/u', ' ', $this->newOpopFio));
        $this->resetErrorBag('newOpopFio');

        if (mb_strlen($fio) < 5 || mb_strlen($fio) > 100) {
            $this->addError('newOpopFio', 'Укажите ФИО, например: Иванов И.И.');
            return;
        }

        $leader = OpopLeader::firstOrCreate(['fio' => $fio]);
        $leader->update(['is_active' => true]);

        $this->signer = self::OPOP_POSITION . '|' . $leader->short_fio;
        $this->newOpopFio = '';
        $this->dispatch('show-toast', message: 'Руководитель ОПОП добавлен', type: 'success');
    }

    public function removeOpopLeader()
    {
        if ((int) $this->documentType !== 4) {
            return;
        }

        $data = explode('|', $this->signer);
        if (($data[0] ?? '') !== self::OPOP_POSITION) {
            return;
        }

        OpopLeader::where('fio', $data[1] ?? '')->update(['is_active' => false]);
        $this->signer = self::DIRECTOR_POSITION . '|' . self::DIRECTOR_FIO;
        $this->dispatch('show-toast', message: 'Руководитель ОПОП убран из списка', type: 'success');
    }

    // Продолжительность (дней, включая первый и последний) считается автоматически
    public function updatedCheckInDate()
    {
        $this->recalcCheckInDuration();
    }

    public function updatedCheckOutDate()
    {
        $this->recalcCheckInDuration();
    }

    private function recalcCheckInDuration(): void
    {
        if ($this->checkInDate === '' || $this->checkOutDate === '') {
            return;
        }

        try {
            $from = Carbon::parse($this->checkInDate)->startOfDay();
            $to = Carbon::parse($this->checkOutDate)->startOfDay();
        } catch (Exception $e) {
            return;
        }

        $this->checkInDuration = $to->gte($from) ? $from->diffInDays($to) + 1 : 0;
    }

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

    // Вид характеристики: текущий (можно менять вручную) и исходный (то, что заказал студент)
    public string $characteristicType = 'positive';           // positive | negative
    public string $originalCharacteristicType = 'positive';   // positive | negative

    public bool $isExpelled = false; // справка отчисленному / выпускнику (шаблон «_otch»)
    public string $leaveOrders = '';
    public bool $showLeaveOrders = true; // выводить блок с приказами в справке

    // Отклонение заявки
    public bool $isShowRejectModal = false;
    public int $rejectRequestId = 0;
    public string $rejectReason = '';

    /**
     * Приказы студента для справки: академический отпуск, выход, отчисление, восстановление.
     * Логика разбора — в App\Support\StudentOrderHistory.
     */
    private function detectLeaveInfo(int $userId, ?string $completionDate = null): array
    {
        $orderRows = DB::table('order_user')
            ->join('orders', 'order_user.order_id', '=', 'orders.id')
            ->where('order_user.user_id', '=', $userId)
            ->whereNull('order_user.deleted_at')
            ->whereNull('orders.deleted_at')
            ->orderBy('orders.order_date')
            ->orderBy('order_user.id')
            ->select('order_user.id', 'orders.id as order_id', 'orders.order_number', 'orders.order_date', 'order_user.notes')
            ->get()
            ->map(fn($row) => (array) $row)
            ->all();

        $leaveRows = DB::table('students_academic_leaves as sal')
            ->join('orders', 'sal.order_id', '=', 'orders.id')
            ->where('sal.user_id', '=', $userId)
            ->whereNull('sal.deleted_at')
            ->whereNull('orders.deleted_at')
            ->select('orders.id as order_id', 'orders.order_number', 'orders.order_date')
            ->get()
            ->map(fn($row) => (array) $row)
            ->all();

        $lastStateId = DB::table('student_state')
            ->where('user_id', '=', $userId)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->value('state_id');

        return StudentOrderHistory::build(
            $orderRows,
            $leaveRows,
            is_null($lastStateId) ? null : (int) $lastStateId,
            GroupStudent::where('user_id', '=', $userId)->exists(),
            $completionDate
        );
    }

    /**
     * Группа студента, который сейчас выведен из групп.
     */
    private function leftGroupId(int $userId): ?int
    {
        $states = DB::table('student_state')
            ->where('user_id', '=', $userId)
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get(['state_id', 'group_id']);

        $last = $states->first();

        if (is_null($last) || !in_array((int) $last->state_id, [1, 2, 3], true)) {
            return null;
        }

        $groupId = $states->first(fn($s) => (int) $s->group_id > 0)?->group_id;

        return $groupId ? (int) $groupId : null;
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
                'student_document_order_requests.quantity',
                'student_document_order_requests.reject_reason',
                'student_document_order_issuances.characteristic_type',
                'student_document_order_issuances.original_characteristic_type',
                'student_document_order_issuances.signer_position',
                'student_document_order_issuances.signer_fio',
                DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) as fio"),
                DB::raw("COALESCE(
                    (SELECT group_name FROM group_student INNER JOIN `groups` ON group_student.group_id = groups.id WHERE group_student.user_id = student_document_order_requests.requester_id ORDER BY group_student.id DESC LIMIT 1),
                    (SELECT CONCAT(g2.group_name, CASE ss.state_id WHEN 1 THEN ' (акад. отпуск)' WHEN 2 THEN ' (отчислен)' ELSE ' (выпускник)' END) FROM student_state ss INNER JOIN `groups` g2 ON ss.group_id = g2.id WHERE ss.user_id = student_document_order_requests.requester_id AND ss.deleted_at IS NULL AND ss.state_id IN (1, 2, 3) AND ss.id = (SELECT MAX(ss2.id) FROM student_state ss2 WHERE ss2.user_id = ss.user_id AND ss2.deleted_at IS NULL))
                ) as group_name"),
                'student_document_order_issuances.confirmiter_id',
                'student_document_order_requests.created_at'
            );

        if ($this->selectedDocumentType != 0) {
            $query->where('student_document_order_requests.document_type', '=', $this->selectedDocumentType);
        }

        if ($this->selectedStatus != 0) {
            $query->where('student_document_order_requests.status', '=', $this->selectedStatus);
        }

        $query->orderBy('student_document_order_requests.created_at', 'desc')
            ->orderBy('student_document_order_issuances.id', 'desc');

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

            if (is_null($groupStudent)) {
                $leaveGroupId = $this->leftGroupId((int) $requesterId);

                if (!is_null($leaveGroupId)) {
                    $groupStudent = DB::table('groups')
                        ->join('work_study_plans', 'groups.work_study_plan_id', '=', 'work_study_plans.id')
                        ->join('teaching_forms', 'work_study_plans.teaching_form_id', '=', 'teaching_forms.id')
                        ->join('specialities', 'work_study_plans.speciality_id', '=', 'specialities.id')
                        ->join('level_educations', 'specialities.level_education_id', '=', 'level_educations.id')
                        ->where('groups.id', '=', $leaveGroupId)
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
                        ->first();
                }

                if (is_null($groupStudent)) {
                    $this->dispatch('show-toast', message: 'Не удалось определить группу студента. Проверьте историю состояний студента.', type: 'error');
                    return;
                }
            }

            $orderUser = DB::table('order_user')
                ->join('orders', 'order_user.order_id', '=', 'orders.id')
                ->where('order_user.user_id', '=', $requesterId)
                ->whereNull('order_user.deleted_at')
                ->whereNull('orders.deleted_at')
                ->where('orders.order_date', '>=', '1990-01-01')
                ->select(
                    'order_user.id',
                    'orders.order_number',
                    'orders.order_date',
                    'orders.order_type_id'
                )
                ->orderByRaw("CASE WHEN order_user.notes = 'Незачисленные' THEN 0 ELSE 1 END")
                ->orderBy('orders.order_date')
                ->orderBy('order_user.id')
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
                    'leave_orders',
                    'is_expelled',
                    'characteristic_type',
                    'original_characteristic_type',
                    'show_leave_orders',
                    'signer_position',
                    'signer_fio'
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

            $this->order = Carbon::parse($orderUser->order_date)->format('d.m.Y') . ' № ' . StudentOrderHistory::cleanNumber($orderUser->order_number);
            $this->trainingPeriod = 'с ' . Carbon::parse($groupStudent->year_admission)->format('d.m.Y') . 'г. по ' . Carbon::parse($groupStudent->year_completion)->format('d.m.Y') . 'г.';

            $this->documentNumber = is_null($studentDocumentOrderIssuance->confirmiter_id)
                ? $this->nextDocumentNumber()
                : (int) $studentDocumentOrderIssuance->document_number;
            $this->checkInDate = (is_null($studentDocumentOrderIssuance->check_in_date)) ? '' : $studentDocumentOrderIssuance->check_in_date;
            $this->checkOutDate = (is_null($studentDocumentOrderIssuance->check_out_date)) ? '' : $studentDocumentOrderIssuance->check_out_date;
            $this->checkInDuration = $studentDocumentOrderIssuance->check_in_duration;
            $this->employmentPlace = $studentDocumentOrderIssuance->employment_place;
            $this->isBudget = $studentDocumentOrderIssuance->is_budget;

            // Характеристика: текущий и исходный вид
            $this->characteristicType = $studentDocumentOrderIssuance->characteristic_type ?: 'positive';
            $this->originalCharacteristicType = $studentDocumentOrderIssuance->original_characteristic_type
                ?: ($studentDocumentOrderIssuance->characteristic_type ?: 'positive');

            // Подписант: для характеристики — сохраняем ранее выбранного ОПОПа; для остальных — только директор
            $defaultSigner = self::DIRECTOR_POSITION . '|' . self::DIRECTOR_FIO;
            $savedPosition = (string) ($studentDocumentOrderIssuance->signer_position ?? '');
            $savedFio = (string) ($studentDocumentOrderIssuance->signer_fio ?? '');

            if ((int) $documentType === 4 && $savedPosition !== '' && $savedFio !== '') {
                $savedSigner = $savedPosition . '|' . $savedFio;
                $this->signer = array_key_exists($savedSigner, $this->signers)
                    ? $savedSigner
                    : $defaultSigner;
            } else {
                $this->signer = $defaultSigner;
            }

            if (is_null($studentDocumentOrderIssuance->confirmiter_id)) {
                $leaveInfo = $this->detectLeaveInfo(
                    (int) $requesterId,
                    Carbon::parse($groupStudent->year_completion)->format('Y-m-d')
                );
                $this->hadAcademicLeave = $leaveInfo['hadLeave'];
                $this->isExpelled = $leaveInfo['isExpelled'] || $leaveInfo['isGraduated'];
                $this->leaveOrders = implode("\n", $leaveInfo['lines']);
                $this->showLeaveOrders = $leaveInfo['hasOrders'];
            } else {
                $this->hadAcademicLeave = (bool) $studentDocumentOrderIssuance->had_academic_leave;
                $this->isExpelled = (bool) $studentDocumentOrderIssuance->is_expelled;
                $this->leaveOrders = (string) $studentDocumentOrderIssuance->leave_orders;
                $this->showLeaveOrders = (bool) $studentDocumentOrderIssuance->show_leave_orders;
            }
            $this->quantity = (int) StudentDocumentOrderRequest::whereKey($studentDocumentOrderRequestId)->value('quantity');
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
            'signer' => ['required', Rule::in(array_keys($this->signers))],
        ];

        if ($this->documentType == 3) {
            $rules += [
                'checkInDate' => 'required',
                'checkOutDate' => 'required',
                'checkInDuration' => 'required|numeric|min:1',
                'employmentPlace' => 'required|string|max:1000',
            ];
        }

        if ($this->documentType == 4) {
            $rules['characteristicType'] = ['required', Rule::in(['positive', 'negative'])];
        }

        $this->validate($rules);

        $isConfirmed = DB::transaction(function () {
            $issuance = StudentDocumentOrderIssuance::lockForUpdate()->findOrFail($this->id);

            $request = StudentDocumentOrderRequest::lockForUpdate()->findOrFail($this->studentDocumentOrderRequestId);

            if (!is_null($issuance->confirmiter_id) || (int) $request->status === 3) {
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
            $issuance->show_leave_orders = $this->showLeaveOrders && trim($this->leaveOrders) !== '';
            $issuance->is_budget = $this->isBudget;
            $issuance->is_expelled = $this->documentType == 1 && $this->isExpelled;

            // Характеристика: сохраняем текущий вид + фиксируем исходный (тот, что был до правки)
            if ($this->documentType == 4) {
                $issuance->characteristic_type = $this->characteristicType;
                $issuance->original_characteristic_type = $this->originalCharacteristicType;
            }

            $issuance->confirmed_at = now();
            $issuance->confirmiter_id = Auth::user()->id;

            $signerData = explode('|', $this->signer);
            $issuance->signer_position = $signerData[0];
            $issuance->signer_fio = $signerData[1] ?? '';

            $issuance->update();

            $request->status = 2;
            $request->update();

            return true;
        });

        $this->isShowModalDialog = false;

        if ($isConfirmed) {
            $this->dispatch('show-toast', message: 'Заявка успешно подтверждена!', type: 'success');
        } else {
            $this->dispatch('show-toast', message: 'Заявка уже была обработана ранее', type: 'info');
        }
    }

    public function openReject($studentDocumentOrderRequestId)
    {
        $this->resetErrorBag();
        $this->resetValidation();
        $this->rejectRequestId = (int) $studentDocumentOrderRequestId;
        $this->rejectReason = '';
        $this->isShowRejectModal = true;
    }

    public function reject()
    {
        $this->validate(
            ['rejectReason' => 'required|string|min:3|max:1000'],
            [
                'rejectReason.required' => 'Укажите причину отклонения',
                'rejectReason.min' => 'Причина слишком короткая',
                'rejectReason.max' => 'Причина не должна превышать 1000 символов',
            ]
        );

        $result = DB::transaction(function () {
            $request = StudentDocumentOrderRequest::lockForUpdate()->find($this->rejectRequestId);

            if (is_null($request)) {
                return 'missing';
            }

            if ((int) $request->status !== 1) {
                return 'processed';
            }

            $request->status = 3;
            $request->reject_reason = trim($this->rejectReason);
            $request->rejected_at = now();
            $request->rejected_by = Auth::id();
            $request->save();

            return 'ok';
        });

        $this->isShowRejectModal = false;
        $this->rejectReason = '';

        if ($result === 'ok') {
            $this->dispatch('show-toast', message: 'Заявка отклонена', type: 'success');
        } elseif ($result === 'processed') {
            $this->dispatch('show-toast', message: 'Заявка уже была обработана ранее', type: 'info');
        } else {
            $this->dispatch('show-toast', message: 'Заявка не найдена', type: 'error');
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