<?php

namespace App\Livewire\Employee;

use App\Models\OperatingActivity as ModelsOperatingActivity;
use App\Models\OperatingActivityFile;
use App\Models\RectorateAndCouncilDecision;
use App\Models\UserActivityDirection;
use App\Models\UserData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class OperatingActivity extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $isCreateOrEditModalVisible = false;
    public $isDescriptionModalVisible = false;

    public $isEdit = false;

    public $selectedYear = 0;
    public $selectedMonth = 0;
    public $selectedWeek = '';
    public $selectedUserId = 0;
    public $selectedWeekStartDate = '';
    public $selectedWeekEndDate = '';

    public $listOfYears = [];
    public $listOfMonths = [];
    public $listOfWeeks = [];
    public $listOfEmployees = [];
    public $listOfActivityDirections = [];

    //Переменные модели
    public $id = 0;
    public $activityDirection = '';
    public $description = '';
    public $status = 0;
    public $weekStartDate = '';
    public $weekEndDate = '';

    public $executor = '';
    public $week = '';

    public $files = [];
    public $storedFiles = [];
    public $rand = 1;

    public function mount()
    {
        //Установка начальных значений
        $this->selectedYear = Carbon::now()->format('Y');
        $this->selectedMonth = Carbon::now()->format('m.Y');

        if (!Auth::user()->hasPermissionTo('rector')) {
            $this->selectedUserId = Auth::user()->id;
        }
    }

    public function render()
    {
        //Получение списков
        $this->listOfYears = ModelsOperatingActivity::select(DB::raw("DATE_FORMAT(week_start_date, '%Y') as year"))->distinct()->get();
        $this->listOfMonths = ModelsOperatingActivity::where('week_start_date', 'like', $this->selectedYear . '%')
            ->select(DB::raw("DATE_FORMAT(week_start_date, '%m.%Y') as month"))
            ->distinct()
            ->get();

        $listOfWeeks = ModelsOperatingActivity::where('week_start_date', 'like', $this->selectedYear . '%');

        if (!empty($this->selectedMonth)) {
            $listOfWeeks = $listOfWeeks->where('week_start_date', 'like', Carbon::createFromFormat('m.Y', $this->selectedMonth)->format('Y-m') . '%');
        }

        $listOfWeeks = $listOfWeeks->select(DB::raw("CONCAT(DATE_FORMAT(week_start_date, '%d.%m.%Y'),' - ', DATE_FORMAT(week_end_date, '%d.%m.%Y')) as week"))
            ->distinct()
            ->get();

        $this->listOfWeeks = $listOfWeeks;

        $this->listOfEmployees = $this->getListOfEmployees();

        $this->listOfActivityDirections = UserActivityDirection::join('activity_directions', 'user_activity_directions.activity_direction_id', '=', 'activity_directions.id')
            ->where('user_activity_directions.user_id', '=', Auth::user()->id)
            ->select('activity_directions.id', 'activity_directions.name')
            ->get();

        //Получение записей для вывода в таблицу
        $operatingActivities = ModelsOperatingActivity::join('users_data', 'operating_activities.user_id', '=', 'users_data.user_id');

        //Фильтр по периоду
        if (!empty($this->selectedWeekStartDate) && !empty($this->selectedWeekEndDate)) {
            $operatingActivities = $operatingActivities->where('operating_activities.week_start_date', '=', $this->selectedWeekStartDate)
                ->where('operating_activities.week_end_date', '=', $this->selectedWeekEndDate);
        }

        //Фильтр по пользователю
        if ($this->selectedUserId != 0) {
            $operatingActivities = $operatingActivities->where('operating_activities.user_id', '=', $this->selectedUserId);
        }

        $operatingActivities = $operatingActivities->select(
            'operating_activities.id',
            'operating_activities.activity_direction',
            'operating_activities.status',
            DB::raw("CONCAT(users_data.last_name,' ', SUBSTRING(users_data.first_name, 1, 1),'.', SUBSTRING(users_data.middle_name, 1, 1), '.') as fio"),
            DB::raw("CONCAT(DATE_FORMAT(operating_activities.week_start_date, '%d.%m.%Y'),' - ', DATE_FORMAT(operating_activities.week_end_date, '%d.%m.%Y')) as week"),
            'operating_activities.user_id',
            'operating_activities.description'
        )
            ->paginate(50);

        return view('livewire.employee.operating-activity', ['operatingActivities' => $operatingActivities]);
    }

    public function setYear()
    {
        $this->selectedYear = $this->selectedYear;

        $this->selectedMonth = 0;
        $this->selectedWeek = 0;
        $this->selectedWeekStartDate = '';
        $this->selectedWeekEndDate = '';
    }

    public function setMonth()
    {
        $this->selectedMonth = $this->selectedMonth;

        $this->selectedWeek = 0;
        $this->selectedWeekStartDate = '';
        $this->selectedWeekEndDate = '';
    }

    public function setWeek()
    {
        $this->selectedWeek = $this->selectedWeek;
        $dates = explode('-', $this->selectedWeek);

        $this->selectedWeekStartDate = Carbon::parse(trim($dates[0]))->startOfWeek()->format('Y-m-d');
        $this->selectedWeekEndDate = Carbon::parse(trim($dates[0]))->endOfWeek()->format('Y-m-d');

        $this->resetPage();
    }

    public function setUserId()
    {
        $this->selectedUserId = $this->selectedUserId;
        $this->resetPage();
    }

    public function show()
    {
        $this->resetFields();

        $this->weekStartDate = Carbon::parse(now())->startOfWeek()->format('Y-m-d');
        $this->weekEndDate = Carbon::parse(now())->endOfWeek()->format('Y-m-d');

        $this->isEdit = false;
        $this->isCreateOrEditModalVisible = true;
    }

    public function edit($id)
    {
        $this->resetFields();

        $modelsOperatingActivity = ModelsOperatingActivity::findOrFail($id);
        $this->id = $id;
        $this->weekStartDate = Carbon::parse($modelsOperatingActivity->week_start_date)->startOfWeek()->format('Y-m-d');
        $this->weekEndDate = Carbon::parse($modelsOperatingActivity->week_end_date)->endOfWeek()->format('Y-m-d');
        $this->activityDirection = $modelsOperatingActivity->activity_direction;
        $this->description = $modelsOperatingActivity->description;
        $this->status = $modelsOperatingActivity->status;

        $operatingActivityFile = OperatingActivityFile::where('operating_activity_id', '=', $id)
            ->select('id', 'file_path', 'filename')
            ->get();

        $this->storedFiles = $operatingActivityFile;

        $this->isEdit = true;
        $this->isCreateOrEditModalVisible = true;
    }

    public function store()
    {
        if (!empty($this->files)) {
            $this->validate([
                'activityDirection' => 'required|string|max:1000',
                'description' => 'required|string|max:5000',
                'status' => 'required|numeric|min:1',
                'weekStartDate' => 'required',
                'weekEndDate' => 'required',
                'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png|max:3072'
            ]);
        } else {
            $this->validate([
                'activityDirection' => 'required|string|max:1000',
                'description' => 'required|string|max:5000',
                'status' => 'required|numeric|min:1',
                'weekStartDate' => 'required',
                'weekEndDate' => 'required'
            ]);
        }

        $operatingActivity = new ModelsOperatingActivity();
        $operatingActivity->activity_direction = $this->activityDirection;
        $operatingActivity->description = $this->description;
        $operatingActivity->status = $this->status;
        $operatingActivity->user_id = Auth::user()->id;
        $operatingActivity->week_start_date = Carbon::parse($this->weekStartDate)->format('Y-m-d');
        $operatingActivity->week_end_date = Carbon::parse($this->weekEndDate)->format('Y-m-d');
        $operatingActivity->save();

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $filePath = $file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
                OperatingActivityFile::create([
                    'operating_activity_id' => $operatingActivity->id,
                    'file_path' => $filePath,
                    'filename' => $file->getClientOriginalName()
                ]);
            }
        }

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно добавлена!', type: 'success');
    }

    public function update()
    {
        if (!empty($this->files)) {
            $this->validate([
                'activityDirection' => 'required|string|max:1000',
                'description' => 'required|string|max:5000',
                'status' => 'required|numeric|min:1',
                'weekStartDate' => 'required',
                'weekEndDate' => 'required',
                'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png|max:3072'
            ]);
        } else {
            $this->validate([
                'activityDirection' => 'required|string|max:1000',
                'description' => 'required|string|max:5000',
                'status' => 'required|numeric|min:1',
                'weekStartDate' => 'required',
                'weekEndDate' => 'required'
            ]);
        }

        $operatingActivity = ModelsOperatingActivity::findOrFail($this->id);
        $operatingActivity->activity_direction = $this->activityDirection;
        $operatingActivity->description = $this->description;
        $operatingActivity->status = $this->status;
        $operatingActivity->week_start_date = Carbon::parse($this->weekStartDate)->format('Y-m-d');
        $operatingActivity->week_end_date = Carbon::parse($this->weekEndDate)->format('Y-m-d');
        $operatingActivity->update();

        if (!empty($this->files)) {
            foreach ($this->files as $file) {
                $filePath = $file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
                OperatingActivityFile::create([
                    'operating_activity_id' => $operatingActivity->id,
                    'file_path' => $filePath,
                    'filename' => $file->getClientOriginalName()
                ]);
            }
        }

        if ($this->activityDirection == 'Решение ректората') {
            $rectorateDecision = RectorateAndCouncilDecision::where('operating_activity_id', '=', $this->id)->first();
            $rectorateDecision->status = $this->status;
            $rectorateDecision->update();
        }

        $this->isCreateOrEditModalVisible = false;
        $this->dispatch('show-toast', message: 'Запись успешно обновлена!', type: 'success');
    }

    #[On('destroy')]
    public function destroy($id)
    {
        $modelsOperatingActivity = ModelsOperatingActivity::findOrFail($id);

        $operatingActivityFile = OperatingActivityFile::where('operating_activity_id', '=', $id)->get();
        if (count($operatingActivityFile) != 0) {
            foreach ($operatingActivityFile as $key => $value) {
                unlink(storage_path('app/public/' . $value->file_path));
            }
        }

        if ($modelsOperatingActivity->activity_direction == 'Решение ректората') {
            $rectorateDecision = RectorateAndCouncilDecision::where('operating_activity_id', '=', $id)->first();
            $rectorateDecision->status = 1;
            $rectorateDecision->operating_activity_id = null;
            $rectorateDecision->executor_id = null;
            $rectorateDecision->update();
        }

        $modelsOperatingActivity->delete();

        $this->dispatch('show-toast', message: 'Запись успешно удалена!', type: 'success');
    }

    public function removeFileFromArray($key)
    {
        unset($this->files[$key]);
        $this->files = array_values($this->files);
    }

    public function removeFileFromStorage($key)
    {
        $operatingActivityFile = OperatingActivityFile::find($key);
        unlink(storage_path('app/public/' . $operatingActivityFile->file_path));
        $operatingActivityFile->delete();

        $this->storedFiles = OperatingActivityFile::where('operating_activity_id', '=', $this->id)
            ->select('id', 'file_path', 'filename')
            ->get();
    }

    public function showDescription($id)
    {
        $modelsOperatingActivity = ModelsOperatingActivity::findOrFail($id);

        $userData = UserData::where('user_id', '=', $modelsOperatingActivity->user_id)
            ->select(DB::raw("CONCAT(users_data.last_name,' ', users_data.first_name,' ', users_data.middle_name) as fio"))
            ->first();

        $operatingActivityFile = OperatingActivityFile::where('operating_activity_id', '=', $id)
            ->select('id', 'file_path', 'filename')
            ->get();

        $this->executor = $userData->fio;
        $this->activityDirection = $modelsOperatingActivity->activity_direction;
        $this->description = $modelsOperatingActivity->description;
        $this->week = Carbon::parse($modelsOperatingActivity->week_start_date)->format('d.m.Y') . '-' . Carbon::parse($modelsOperatingActivity->week_end_date)->format('d.m.Y');
        $this->storedFiles = $operatingActivityFile;

        $this->isDescriptionModalVisible = true;
    }

    public function confirm($id)
    {
        $modelsOperatingActivity = ModelsOperatingActivity::findOrFail($id);

        if ($modelsOperatingActivity->activity_direction == 'Решение ректората') {
            $rectorateDecision = RectorateAndCouncilDecision::where('operating_activity_id', '=', $id)->first();
            $rectorateDecision->status = 3;
            $rectorateDecision->update();
        }

        $modelsOperatingActivity->status = 3;
        $modelsOperatingActivity->update();

        $this->dispatch('show-toast', message: 'Выполнение поставленной задачи успешно подтверждено!', type: 'success');
    }

    public function undoConfirm($id)
    {
        $modelsOperatingActivity = ModelsOperatingActivity::findOrFail($id);

        if ($modelsOperatingActivity->activity_direction == 'Решение ректората') {
            $rectorateDecision = RectorateAndCouncilDecision::where('operating_activity_id', '=', $id)->first();
            $rectorateDecision->status = 1;
            $rectorateDecision->update();
        }

        $modelsOperatingActivity->status = 1;
        $modelsOperatingActivity->update();

        $this->dispatch('show-toast', message: 'Подтверждение выполнения задачи успешно отклонено!', type: 'success');
    }

    private function getListOfEmployees()
    {
        return ModelsOperatingActivity::join('users_data', 'operating_activities.user_id', '=', 'users_data.user_id')
            ->select(
                'operating_activities.user_id',
                DB::raw("CONCAT(users_data.last_name,' ', SUBSTRING(users_data.first_name, 1, 1),'.', SUBSTRING(users_data.middle_name, 1, 1), '.') as fio")
            )
            ->distinct()
            ->orderBy('fio')
            ->get();
    }

    private function resetFields()
    {
        $this->id = 0;
        $this->activityDirection = '';
        $this->description = '';
        $this->status = 0;
        $this->weekStartDate = '';
        $this->weekEndDate = '';
        $this->files = [];

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }
}
