<?php

namespace App\Livewire\Employee;

use App\Http\Controllers\PrintDecisionsRectorateAndCouncilController;
use App\Models\OperatingActivity;
use App\Models\RectorateAndCouncilDecision;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
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
    public $listOfPositions = [];

    public $listOfUsersAndPositions = [
        ['Проректор по учебной и научной работе' => 24829],
        ['Проректор по воспитательной работе и молодежной политике' => 2447],
        ['Проректор по цифрову развитию' => 27414],
        ['Проректор по корпоративному развитию и метакомпетенциям' => 2574],
        ['Директор по маркетингу и инновациям' => 15318],
        ['Руководитель ЦАОП' => 3422],
        ['Руководитель Управления по воспитательной работе и социализации' => 2447],
        ['Руководитель Академии экономики и управления' => 24829],
        ['Руководитель Академии права и государственной службы' => 12474],
        ['Руководитель Академии гуманитарных технологий' => 2241],
        ['Руководитель Академии цифрового развития' => 2574],
        ['Руководитель Академии туризма и индустрии гостеприимства' => 18624],
        ['ОПОП 38.02.01 Экономика и бухгалтерский учет (по отраслям)' => 24829],
        ['ОПОП 38.02.03 Операционная деятельность в логистике' => 19077],
        ['ОПОП 40.02.04 Юриспруденция' => 23377],
        ['ОПОП 43.02.16 Туризм и гостеприимство' => 18624],
        ['ОПОП 09.02.07 Информационные системы и программирование' => 24191],
        ['ОПОП 37.03.01 Психология' => 2241],
        ['ОПОП 38.03.01 Экономика' => 24829],
        ['ОПОП 38.03.02 Менеджмент ' => 17809],
        ['ОПОП 38.03.03 Управление персоналом' => 17809],
        ['ОПОП 43.03.02 Туризм' => 18624],
        ['ОПОП 09.03.03 Прикладная информатика' => 24191],
        ['ОПОП 09.04.03 Прикладная информатика' => 2574],
        ['ОПОП 37.04.01 Психология' => 2241],
        ['ОПОП 38.04.01 Экономика' => 24829],
        ['ОПОП 38.04.02 Менеджмент' => 24829],
        ['ОПОП 40.04.01 Юриспруденция (правосудие по уголовным делам' => 2447],
        ['ОПОП 40.04.01 Юриспруденция (правовое обеспечение государственного и муниципального управления)' => 12474],
        ['Заведующий кафедрой «Психология и управление персоналом»' => 2241],
        ['Заведующий кафедрой «Гуманитарные дисциплины»' => 2241],
        ['Заведующий кафедрой «Физическое воспитание»' => 2277],
        ['Заведующий кафедрой «Управление организационными системами»' => 2190],
        ['Заведующий кафедрой «Финансы, бухучет и налогообложение»' => 28696],
        ['Заведующий кафедрой «Гражданско-правовые дисциплины»' => 2667],
        ['Заведующий кафедрой «Государственно - правовые дисциплины»' => 15857],
        ['Заведующий кафедрой «Уголовно-правовые дисциплины»' => 2447],
        ['Заведующий кафедрой «ИТ и прикладная математика»' => 2574],
        ['Заведующий кафедрой «Экономика, туризм и инновационные рыночные исследования»' => 18624],
        ['Руководитель УМО' => 27421],
        ['Начальник службы безопасности' => 2447],
        ['Главный бухгалтер' => 5290],
        ['Начальник ФЭО' => 28702],
        ['Начальник отдела кадров' => 17809],
        ['Начальник юридического отдела' => 14733],
        ['Начальник отдела соцкультбыта' => 28704],
        ['Ответственный секретарь приемной комиссии' => 18717],
        ['Руководитель ИБЦ' => 8399],
        ['Начальник хозяйственного отдела' => 2656],
        ['Директор ЧПОУ (Сальск)' => 25694],
        ['Директор ЧПОУ (Донецк)' => 13693],
        ['Директор медицинского колледжа' => 23655],
        ['Директор колледжа экономики и логистики' => 19077],
        ['Директор правового колледжа' => 23377],
        ['Директор IT колледжа' => 24191],
        ['Директор колледжа туризма и гостеприимства' => 18624],
        ['Проектный менеджер центра глобализации' => 24940]
    ];

    public function mount()
    {
        //Установка начальных значений
        $this->selectedYear = Carbon::now()->format('Y');
        $this->selectedMonth = Carbon::now()->format('m.Y');
    }

    public function render()
    {
        if (count($this->listOfPositions) == 0) {
            foreach ($this->listOfUsersAndPositions as $item) {
                foreach ($item as $key => $value) {
                    $this->listOfPositions[] = $key;
                }
            }
        }

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

        $this->executorId = $this->findValueByKey($this->listOfUsersAndPositions, $this->position);

        RectorateAndCouncilDecision::create([
            'date_decision' => Carbon::parse($this->dateDecision)->format('Y-m-d'),
            'number_decision' => $this->numberDecision,
            'position' => $this->position,
            'task' => $this->task,
            'due_date' => Carbon::parse($this->dueDate)->format('Y-m-d'),
            'status' => 1,
            'category' => 1,
            'executor_id' => $this->executorId,
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

        $this->executorId = $this->findValueByKey($this->listOfUsersAndPositions, $this->position);

        $rectorateDecision = RectorateAndCouncilDecision::findOrFail($this->id);
        $rectorateDecision->date_decision = Carbon::parse($this->dateDecision)->format('Y-m-d');
        $rectorateDecision->number_decision = $this->numberDecision;
        $rectorateDecision->position = $this->position;
        $rectorateDecision->task = $this->task;
        $rectorateDecision->due_date = Carbon::parse($this->dueDate)->format('Y-m-d');
        $rectorateDecision->executor_id = $this->executorId;
        $rectorateDecision->user_id = Auth::user()->id;
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
        $operatingActivity->activity_direction = 'Исполнение решений ректората';
        $operatingActivity->description = 'В соответствии с решением ректората от ' . Carbon::parse($rectorateDecision->date_decision)->format('d.m.Y') . ' №' . $rectorateDecision->number_decision . ' ...';
        $operatingActivity->status = 2;
        $operatingActivity->user_id = Auth::user()->id;
        $operatingActivity->week_start_date = Carbon::parse(now())->startOfWeek()->format('Y-m-d');
        $operatingActivity->week_end_date = Carbon::parse(now())->endOfWeek()->format('Y-m-d');
        $operatingActivity->save();

        $rectorateDecision->status = 2;

        $operatingActivityIds = [];

        if (!is_null($rectorateDecision->operating_activity_id)) {
            $jsonData = json_decode($rectorateDecision->operating_activity_id, true);

            foreach ($jsonData as $value) {
                $operatingActivityIds[] = (int)$value;
            }

            $operatingActivityIds[] = $operatingActivity->id;
        } else {
            $operatingActivityIds[] = $operatingActivity->id;
        }

        $rectorateDecision->operating_activity_id = json_encode($operatingActivityIds);
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

    public function downloadReport()
    {
        if ($this->selectedDueDate == 0) {
            $this->dispatch('show-toast', message: 'Для формирования отчета необходимо выбрать срок исполнения!', type: 'error');
        } else {
            $rectorateDecisions = RectorateAndCouncilDecision::where('due_date', '=', Carbon::parse($this->selectedDueDate)->format('Y-m-d'))
                ->orderBy('position')
                ->get();
                
            $report = new PrintDecisionsRectorateAndCouncilController();
            $data = $report->downloadReport($rectorateDecisions);

            return Response::download($data['tempFile'], $data['filename'])->deleteFileAfterSend(true);
        }
    }

    function findValueByKey($array, $position)
    {
        $tmp = [];
        foreach ($array as $item) {
            foreach ($item as $key => $value) {
                $tmp[$key] = $value;
            }
        }

        return $tmp[$position];
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
