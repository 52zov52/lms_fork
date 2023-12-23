<?php

namespace App\Livewire;

use App\Models\DecisionsRectorateAndCouncil as ModelsDecisionsRectorateAndCouncil;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class DecisionsRectorateAndCouncil extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $positions = [
        'Первый проректор',
        'Проректор по общим вопросам',
        'Проректор по непрерывному образованию',
        'Проректор по науке',
        'Проректор по международной деятельности',
        'Проректор по цифровой трансформации',
        'Проректор по евразийской интеграции',
        'Директор по маркетингу и цифровизации',
        'Академический директор',
        'Руководитель ЦАОП',
        'Руководитель Управления по воспитательной работе и социализации',
        'Руководитель Академии экономики и управления',
        'Руководитель Академии права и государственной службы',
        'Руководитель Академии гуманитарных технологий',
        'Руководитель Академии цифрового развития',
        'Руководитель АПО',
        'Координатор инновационных проектов',
        'Руководитель сектора практики ВО',
        'Специалист по внедрению инновационных образовательных технологий, менеджер по организации практики СПО',
        'ОПОП 38.02.01 Экономика и бухгалтерский учет (по отраслям)',
        'ОПОП 38.02.03 Операционная деятельность в логистике',
        'ОПОП 40.02.01 Право и организация социального обеспечения',
        'ОПОП 43.02.10 Туризм',
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
        'Заведующий кафедрой «ИТ и прикладная математика»',
        'Заведующий кафедрой «Психология и управление персоналом»',
        'Заведующий кафедрой «Международный менеджмент»',
        'Заведующий кафедрой «Финансы, бухучет и налогообложение»',
        'Заведующий кафедрой «Экономика и инновационные рыночные исследования»',
        'Заведующий кафедрой «Уголовно-правовые дисциплины»',
        'Заведующий кафедрой «Гражданско-правовые дисциплины»',
        'Заведующий кафедрой «Государственно - правовые дисциплины»',
        'Заведующий кафедрой «Физическое воспитание»',
        'Руководитель УМО',
        'Начальник службы безопасности',
        'Главный бухгалтер',
        'Начальник ФЭО',
        'Начальник отдела кадров',
        'Начальник юридического отдела',
        'Начальник отдела соцкультбыта',
        'Ответственный секретарь приемной комиссии',
        'Руководитель ИБЦ',
        'Начальник хозяйственного отдела'
    ];

    public $id = 0;
    public $dateDecision = 0;
    public $numberDecision = 0;
    public $position = '';
    public $task = '';
    public $description = '';
    public $dueDate = 0;
    public $status = 0;
    public $category = 0;
    public $file = null;
    public $userId = 0;

    public $isCreatingAndEditingModalFormVisible = false, $isDescriptionModalFormVisible = false, $isFilterModalFormVisible = false, $isConfirmDeleteRecordModalVisible = false;
    public $isEdit = false;
    public $isAlertVisible = false;

    public $filterPosition = '';
    public $filterDueDate = '';
    public $filterStatus = '';
    public $filterCategory = '';
    public $filterDateAndNumberDecision = '';

    public $isVisibleDateDecisionField = false;
    public $isVisibleNumberDecisionField = false;
    public $isVisibleDueDateField = false;

    public function render()
    {
        $listOfDateDecision = ModelsDecisionsRectorateAndCouncil::select('date_decision')->whereNotNull('date_decision')->distinct()->get();
        $listOfNumberDecision = ModelsDecisionsRectorateAndCouncil::select('number_decision')->whereNotNull('number_decision')->distinct()->get();
        $listOfDueDate = ModelsDecisionsRectorateAndCouncil::select('due_date')->whereNotNull('due_date')->distinct()->get();

        $catologDueDates = ModelsDecisionsRectorateAndCouncil::select('due_date')->distinct()->get();
        $catologDateAndNumberDecisions = ModelsDecisionsRectorateAndCouncil::select(DB::raw("CONCAT(DATE_FORMAT(date_decision, '%d.%m.%Y'),' №',number_decision) AS date_and_number_decision"))
            ->whereNotNull('date_decision')
            ->whereNotNull('number_decision')
            ->distinct()->get();

        $decisionsRectorateAndCouncil = ModelsDecisionsRectorateAndCouncil::select("*");

        if (!empty($this->filterPosition) && empty($this->filterDueDate) && empty($this->filterStatus)) {
            $decisionsRectorateAndCouncil->where('position', '=', $this->filterPosition);
        }

        if (empty($this->filterPosition) && !empty($this->filterDueDate) && empty($this->filterStatus)) {
            $decisionsRectorateAndCouncil->where('due_date', 'like', '%' . $this->filterDueDate . '%');
        }

        if (empty($this->filterPosition) && empty($this->filterDueDate) && !empty($this->filterStatus)) {
            $decisionsRectorateAndCouncil->where('status', '=', $this->filterStatus);
        }

        if (!empty($this->filterPosition) && empty($this->filterDueDate) && !empty($this->filterStatus)) {
            $decisionsRectorateAndCouncil->where('position', '=', $this->filterPosition);
            $decisionsRectorateAndCouncil->where('status', '=', $this->filterStatus);
        }

        if (!empty($this->filterPosition) && !empty($this->filterDueDate) && !empty($this->filterStatus)) {
            $decisionsRectorateAndCouncil->where('position', '=', $this->filterPosition);
            $decisionsRectorateAndCouncil->where('due_date', 'like', '%' . $this->filterDueDate . '%');
            $decisionsRectorateAndCouncil->where('status', '=', $this->filterStatus);
        }

        if (!empty($this->filterCategory)) {
            $decisionsRectorateAndCouncil->where('category', '=', $this->filterCategory);
        }

        if (!empty($this->filterDateAndNumberDecision)) {
            $decisionsRectorateAndCouncil->select(DB::raw("CONCAT(DATE_FORMAT(date_decision, '%d.%m.%Y'), ' №', number_decision) AS date_and_number_decision"))
                ->having('date_and_number_decision', '=', $this->filterDateAndNumberDecision);
        }

        $decisionsRectorateAndCouncil = $decisionsRectorateAndCouncil->select('id', 'position', 'task', 'due_date', 'category',DB::raw("CONCAT(DATE_FORMAT(date_decision, '%d.%m.%Y'),' №',number_decision) AS date_and_number_decision"))->orderBy('position')->paginate(50);

        return view('livewire.decisions-rectorate-and-council', [
            'listOfDateDecision' => $listOfDateDecision,
            'listOfNumberDecision' => $listOfNumberDecision,
            'listOfDueDate' => $listOfDueDate,
            'decisionsRectorateAndCouncil' => $decisionsRectorateAndCouncil,
            'positions' => $this->positions,
            'catologDueDates' => $catologDueDates,
            'catologDateAndNumberDecisions' => $catologDateAndNumberDecisions
        ]);
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function visibleDateDecisionField(): void
    {
        $this->dateDecision = ($this->isVisibleDateDecisionField) ? 0 : '';
        $this->isVisibleDateDecisionField = ($this->isVisibleDateDecisionField) ? false : true;
    }

    public function visibleNumberDecisionField(): void
    {
        $this->numberDecision = ($this->isVisibleNumberDecisionField) ? 0 : '';
        $this->isVisibleNumberDecisionField = ($this->isVisibleNumberDecisionField) ? false : true;
    }

    public function visibleDueDateField(): void
    {
        $this->dueDate = ($this->isVisibleDueDateField) ? 0 : '';
        $this->isVisibleDueDateField = ($this->isVisibleDueDateField) ? false : true;
    }

    public function creatingAndEditingModalFormVisible($isEdit, $id = 0): void
    {
        $this->resetFields();

        $this->isEdit = $isEdit;

        if ($this->isEdit)
            $this->edit($id);

        $this->isCreatingAndEditingModalFormVisible = true;
    }

    public function confirmDeleteRecordModalVisible($id)
    {
        $this->id = $id;
        $this->isConfirmDeleteRecordModalVisible = true;
    }

    public function descriptionModalFormVisible($id): void
    {
        $decisionsRectorateAndCouncil = ModelsDecisionsRectorateAndCouncil::findOrFail($id);
        $this->description = $decisionsRectorateAndCouncil->description;
        $this->file = $decisionsRectorateAndCouncil->file_path;

        $this->isDescriptionModalFormVisible = true;
    }

    public function filterModalFormVisible(): void
    {
        $this->isFilterModalFormVisible = true;
    }

    public function setFilterPosition(): void
    {
        $this->filterPosition = $this->filterPosition;
    }

    public function setFilterDueDate(): void
    {
        $this->filterDueDate = $this->filterDueDate;
    }

    public function setFilterStatus(): void
    {
        $this->filterStatus = $this->filterStatus;
    }

    public function setFilterCategory(): void
    {
        $this->filterCategory = $this->filterCategory;
    }

    public function setFilterDateAndNumberDecision(): void
    {
        $this->filterDateAndNumberDecision = $this->filterDateAndNumberDecision;
    }

    public function store(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'dateDecision' => 'required|string',
                'numberDecision' => 'required|string',
                'position' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'dueDate' => 'required|string',
                'status' => 'required|numeric|min:1',
                'category' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx|max:3072',
            ]);
        } else {
            $this->validate([
                'dateDecision' => 'required|string',
                'numberDecision' => 'required|string',
                'position' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'dueDate' => 'required|string',
                'status' => 'required|numeric|min:1',
                'category' => 'required|numeric|min:1',
            ]);
        }

        $filename = (!is_null($this->file)) ? $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public') : null;

        ModelsDecisionsRectorateAndCouncil::create([
            'date_decision' => Carbon::parse($this->dateDecision)->format('Y-m-d'),
            'number_decision' => $this->numberDecision,
            'position' => $this->position,
            'task' => $this->task,
            'description' =>  $this->description,
            'due_date' =>  $this->dueDate,
            'status' => $this->status,
            'category' => $this->category,
            'file_path' =>  $filename,
            'user_id' => Auth::user()->id
        ]);

        $this->isCreatingAndEditingModalFormVisible = false;
        $this->isAlertVisible = true;
        $this->resetFields();

        session()->flash('message-success', 'Запись успешно добавлена!');
    }

    public function edit($id): void
    {
        $this->id = $id;

        $decisionsRectorateAndCouncil = ModelsDecisionsRectorateAndCouncil::findOrFail($this->id);
        $this->dateDecision = $decisionsRectorateAndCouncil->date_decision;
        $this->numberDecision = $decisionsRectorateAndCouncil->number_decision;
        $this->position = $decisionsRectorateAndCouncil->position;
        $this->task = $decisionsRectorateAndCouncil->task;
        $this->description = $decisionsRectorateAndCouncil->description;
        $this->dueDate = $decisionsRectorateAndCouncil->due_date;
        $this->status = $decisionsRectorateAndCouncil->status;
        $this->category = $decisionsRectorateAndCouncil->category;
    }

    public function update(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'dateDecision' => 'required|string',
                'numberDecision' => 'required|string',
                'position' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'dueDate' => 'required|string',
                'status' => 'required|numeric|min:1',
                'category' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx|max:3072',
            ]);
        } else {
            $this->validate([
                'dateDecision' => 'required|string',
                'numberDecision' => 'required|string',
                'position' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'dueDate' => 'required|string',
                'status' => 'required|numeric|min:1',
                'category' => 'required|numeric|min:1',
            ]);
        }

        $decisionsRectorateAndCouncil = ModelsDecisionsRectorateAndCouncil::findOrFail($this->id);
        $decisionsRectorateAndCouncil->date_decision = Carbon::parse($this->dateDecision)->format('Y-m-d');
        $decisionsRectorateAndCouncil->number_decision = $this->numberDecision;
        $decisionsRectorateAndCouncil->position = $this->position;
        $decisionsRectorateAndCouncil->task = $this->task;
        $decisionsRectorateAndCouncil->description = $this->description;
        $decisionsRectorateAndCouncil->due_date = $this->dueDate;
        $decisionsRectorateAndCouncil->status = $this->status;
        $decisionsRectorateAndCouncil->category = $this->category;

        if (!is_null($this->file)) {
            if (!is_null($decisionsRectorateAndCouncil->file_path)) {
                unlink(storage_path('app/public/' . $decisionsRectorateAndCouncil->file_path));
            }

            $filename = $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
            $decisionsRectorateAndCouncil->file_path = $filename;
        }

        $decisionsRectorateAndCouncil->update();

        $this->isCreatingAndEditingModalFormVisible = false;
        $this->isAlertVisible = true;
        $this->resetFields();

        session()->flash('message-success', 'Запись успешно отредактирована!');
    }

    public function destroy(): void
    {
        $decisionsRectorateAndCouncil = ModelsDecisionsRectorateAndCouncil::findOrFail($this->id);
        $decisionsRectorateAndCouncil->delete();

        $this->isConfirmDeleteRecordModalVisible = false;
        $this->isAlertVisible = true;

        session()->flash('message-success', 'Запись успешно удалена!');
    }

    public function confirmDecisions($id): void
    {
        $decisionsRectorateAndCouncil = ModelsDecisionsRectorateAndCouncil::findOrFail($id);
        $decisionsRectorateAndCouncil->status = 3;
        $decisionsRectorateAndCouncil->update();

        session()->flash('message-success', 'Запись успешно обновлена!');
    }

    public function resetFields(): void
    {
        $this->dateDecision = 0;
        $this->numberDecision = 0;
        $this->position = '';
        $this->task = '';
        $this->description = '';
        $this->dueDate = 0;
        $this->status = 0;
        $this->category = 0;
        $this->file = null;

        $this->isVisibleDateDecisionField = false;
        $this->isVisibleNumberDecisionField = false;
        $this->isVisibleDueDateField = false;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
