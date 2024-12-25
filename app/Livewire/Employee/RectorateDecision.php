<?php

namespace App\Livewire\Employee;

use App\Models\OperatingActivity;
use App\Models\RectorateAndCouncilDecision;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class RectorateDecision extends Component
{
    use WithPagination;

    public $isCreateOrEditModalVisible = false;

    public $isEdit = false;

    public $selectedYear = 0;
    public $selectedMonth = 0;
    public $selectedDueDate = 0;
    public $selectedPosition = 0;

    //Переменные модели
    public $id = 0;
    public $dateDecision = '';
    public $numberDecision = '';
    public $position = '';
    public $task = '';
    public $dueDate = '';
    public $status = 0;
    public $category = 1;
    public $operatingActivityId = 0;
    public $executorId = 0;
    public $userId = 0;

    public $listOfYears = [];
    public $listOfMonths = [];
    public $listOfDueDates = [];

    public $listOfPositions = [
        'Проректор по учебной и научной работе',
        'Проректор по воспитательной работе и молодежной политике',
        'Проректор по цифрову развитию',
        'Проректор по корпоративному развитию и метакомпетенциям',
        'Проректор по евразийской интеграции',
        'Директор по маркетингу и инновациям',
        'Академический директор',
        'Руководитель ЦАОП',
        'Руководитель Управления по воспитательной работе и социализации',
        'Руководитель Академии экономики и управления',
        'Руководитель Академии права и государственной службы',
        'Руководитель Академии гуманитарных технологий',
        'Руководитель Академии цифрового развития',
        'Руководитель Академии туризма и индустрии гостеприимства',
        'Руководитель АПО',
        'Координатор инновационных проектов',
        'Руководитель сектора практики ВО',
        'Специалист по внедрению инновационных образовательных технологий, менеджер по организации практики СПО',
        'ОПОП 38.02.01 Экономика и бухгалтерский учет (по отраслям)',
        'ОПОП 38.02.03 Операционная деятельность в логистике',
        'ОПОП 40.02.04 Юриспруденция',
        'ОПОП 43.02.16 Туризм и гостеприимство',
        'ОПОП 09.02.07 Информационные системы и программирование',
        'ОПОП 37.03.01 Психология',
        'ОПОП 38.03.01 Экономика',
        'ОПОП 38.03.02 Менеджмент ',
        'ОПОП 38.03.03 Управление персоналом',
        'ОПОП 43.03.02 Туризм',
        'ОПОП 09.03.03 Прикладная информатика',
        'ОПОП 09.04.03 Прикладная информатика',
        'ОПОП 37.04.01 Психология',
        'ОПОП 38.04.01 Экономика',
        'ОПОП 38.04.02 Менеджмент',
        'ОПОП 40.04.01 Юриспруденция (правосудие по уголовным делам',
        'ОПОП 40.04.01 Юриспруденция (правовое обеспечение государственного и муниципального управления)',
        'Заведующий кафедрой «Психология и управление персоналом»',
        'Заведующий кафедрой «Гуманитарные дисциплины»',
        'Заведующий кафедрой «Физическое воспитание»',
        'Заведующий кафедрой «Управление организационными системами»',
        'Заведующий кафедрой «Финансы, бухучет и налогообложение»',
        'Заведующий кафедрой «Гражданско-правовые дисциплины»',
        'Заведующий кафедрой «Государственно - правовые дисциплины»',
        'Заведующий кафедрой «Уголовно-правовые дисциплины»',
        'Заведующий кафедрой «ИТ и прикладная математика»',
        'Заведующий кафедрой «Экономика, туризм и инновационные рыночные исследования»',
        'Руководитель УМО',
        'Начальник службы безопасности',
        'Главный бухгалтер',
        'Начальник ФЭО',
        'Начальник отдела кадров',
        'Начальник юридического отдела',
        'Начальник отдела соцкультбыта',
        'Ответственный секретарь приемной комиссии',
        'Руководитель ИБЦ',
        'Начальник хозяйственного отдела',
        'Директор ЧПОУ (Сальск)',
        'Директор ЧПОУ (Донецк)',
        'Директор медицинского колледжа',
        'Директор колледжа экономики и логистики',
        'Директор правового колледжа',
        'Директор IT колледжа',
        'Директор колледжа туризма и гостеприимства'
    ];

    public function mount()
    {
        //Установка начальных значений
        $this->selectedYear = Carbon::now()->format('Y');
        $this->selectedMonth = Carbon::now()->format('m.Y');
    }

    public function render()
    {
        //Получение списков
        $this->listOfYears = RectorateAndCouncilDecision::where('category', '=', 1)
            ->select(DB::raw("DATE_FORMAT(due_date, '%Y') as year"))
            ->distinct()
            ->get();

        $this->listOfMonths = RectorateAndCouncilDecision::where('category', '=', 1)
            ->where('due_date', 'like', $this->selectedYear . '%')
            ->select(DB::raw("DATE_FORMAT(due_date, '%m.%Y') as month"))
            ->orderBy('month', 'ASC')
            ->distinct()
            ->get();

        $this->listOfDueDates = RectorateAndCouncilDecision::where('category', '=', 1)
            ->where('due_date', 'like', $this->selectedYear . '%')
            ->where('due_date', 'like', Carbon::createFromFormat('m.Y', $this->selectedMonth)->format('Y-m') . '%')
            ->select(DB::raw("DATE_FORMAT(due_date, '%d.%m.%Y') as due_date"))
            ->orderBy('due_date', 'ASC')
            ->distinct()
            ->get();

        $rectorateDecisions = RectorateAndCouncilDecision::where('category', '=', 1);

        //Фильтр по сроку исполнения
        if ($this->selectedDueDate != 0) {
            $rectorateDecisions = $rectorateDecisions->where('due_date', '=', Carbon::parse($this->selectedDueDate)->format('Y-m-d'));
        }

        //Фильтр по должности исполнителя
        if ($this->selectedPosition != 0) {
            $rectorateDecisions = $rectorateDecisions->where('position', '=', $this->selectedPosition);
        }

        $rectorateDecisions = $rectorateDecisions->select(
            'id',
            DB::raw("CONCAT(DATE_FORMAT(date_decision, '%d.%m.%Y'),' №',number_decision) AS date_and_number_decision"),
            'position',
            'task',
            'due_date',
            'status',
            'operating_activity_id',
            'executor_id'
        )
            ->orderBy('due_date', 'DESC')
            ->paginate(50);

        return view('livewire.employee.rectorate-decision', [
            'rectorateDecisions' => $rectorateDecisions
        ]);
    }

    public function setYear()
    {
        $this->selectedYear = $this->selectedYear;
        $this->selectedMonth = 0;
        $this->selectedDueDate = 0;
        $this->resetPage();
    }

    public function setMonth()
    {
        $this->selectedMonth = $this->selectedMonth;
        $this->selectedDueDate = 0;
        $this->resetPage();
    }

    public function setDueDate()
    {
        $this->selectedDueDate = $this->selectedDueDate;
        $this->resetPage();
    }

    public function setPosition()
    {
        $this->selectedPosition = $this->selectedPosition;
        $this->resetPage();
    }

    public function show()
    {
        $this->resetFields();

        $this->isEdit = false;
        $this->isCreateOrEditModalVisible = true;
    }

    public function edit($id)
    {
        $this->resetFields();

        $rectorateDecision = RectorateAndCouncilDecision::findOrFail($id);
        $this->id = $id;
        $this->dateDecision = $rectorateDecision->date_decision;
        $this->numberDecision = $rectorateDecision->number_decision;
        $this->position = $rectorateDecision->position;
        $this->task = $rectorateDecision->task;
        $this->dueDate = $rectorateDecision->due_date;

        $this->isEdit = true;
        $this->isCreateOrEditModalVisible = true;
    }

    public function store()
    {
        $this->validate([
            'dateDecision' => 'required',
            'numberDecision' => 'required',
            'position' => 'required',
            'task' => 'required|string|max:1500',
            'dueDate' => 'required'
        ]);

        RectorateAndCouncilDecision::create([
            'date_decision' => Carbon::parse($this->dateDecision)->format('Y-m-d'),
            'number_decision' => $this->numberDecision,
            'position' => $this->position,
            'task' => $this->task,
            'due_date' => Carbon::parse($this->dueDate)->format('Y-m-d'),
            'status' => 1,
            'category' => 1,
            'user_id' => Auth::user()->id
        ]);

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно добавлена!', type: 'success');
    }

    public function update()
    {
        $this->validate([
            'dateDecision' => 'required',
            'numberDecision' => 'required',
            'position' => 'required',
            'task' => 'required|string|max:1500',
            'dueDate' => 'required'
        ]);

        $rectorateDecision = RectorateAndCouncilDecision::findOrFail($this->id);
        $rectorateDecision->date_decision = Carbon::parse($this->dateDecision)->format('Y-m-d');
        $rectorateDecision->number_decision = $this->numberDecision;
        $rectorateDecision->position = $this->position;
        $rectorateDecision->task = $this->task;
        $rectorateDecision->due_date = Carbon::parse($this->dueDate)->format('Y-m-d');
        $rectorateDecision->update();

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
    }

    #[On('destroy')]
    public function destroy($id)
    {
        $rectorateDecision = RectorateAndCouncilDecision::findOrFail($id);
        $rectorateDecision->delete();

        $this->dispatch('show-toast', message: 'Запись успешно удалена!', type: 'success');
    }

    #[On('submitReport')]
    public function submitReport($id)
    {
        $rectorateDecision = RectorateAndCouncilDecision::findOrFail($id);

        $operatingActivity = new OperatingActivity();
        $operatingActivity->activity_direction = 'Решение ректората';
        $operatingActivity->description = 'В соответствии с решением ректората от ' . Carbon::parse($rectorateDecision->date_decision)->format('d.m.Y') . ' №' . $rectorateDecision->number_decision . ' ...';
        $operatingActivity->status = 2;
        $operatingActivity->user_id = Auth::user()->id;
        $operatingActivity->week_start_date = Carbon::parse(now())->startOfWeek()->format('Y-m-d');
        $operatingActivity->week_end_date = Carbon::parse(now())->endOfWeek()->format('Y-m-d');
        $operatingActivity->save();

        $rectorateDecision->status = 2;
        $rectorateDecision->operating_activity_id = $operatingActivity->id;
        $rectorateDecision->executor_id = Auth::user()->id;
        $rectorateDecision->update();

        return redirect()->route('employee.operating.activity');
    }

    public function confirm($id)
    {
        $rectorateDecision = RectorateAndCouncilDecision::findOrFail($id);
        $rectorateDecision->status = 3;
        $rectorateDecision->update();

        $this->dispatch('show-toast', message: 'Выполнение поставленной задачи успешно подтверждено!', type: 'success');
    }

    public function undoConfirm($id)
    {
        $rectorateDecision = RectorateAndCouncilDecision::findOrFail($id);
        $rectorateDecision->status = 1;
        $rectorateDecision->update();

        $this->dispatch('show-toast', message: 'Подтверждение выполнения задачи успешно отклонено!', type: 'success');
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->dateDecision = '';
        $this->numberDecision = '';
        $this->position = '';
        $this->task = '';
        $this->dueDate = '';
        $this->status = 0;
        $this->category = 1;
        $this->operatingActivityId = 0;
        $this->executorId = 0;
        $this->userId = 0;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
