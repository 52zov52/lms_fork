<?php

namespace App\Livewire;

use App\Models\OperatingActivity as ModelsOperatingActivity;
use App\Models\OperatingActivityFile;
use App\Models\UserActivityDirection;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class OperatingActivity extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $id = 0;
    public $activityDirection = 0;
    public $task = '';
    public $description = '';
    public $status = 0;
    public $file_path = '';
    public $files = [];
    public $userFiles = [];
    public $userId = 0;
    public $week = '';

    public string $filename = '';
    public int $rand = 1;

    public $isCreatingAndEditingModalFormVisible = false, $isDescriptionModalFormVisible = false, $isFilterModalFormVisible = false, $isAlertVisible = false, $isConfirmDeleteRecordModalVisible = false;
    public $isEdit = false;

    public $filterYear = '';
    public $filterMonthYear = '';

    public $filterWeeks = '';
    public $filterActivityDirection = '';
    public $filterExecutor = '';
    public $filterStatus = '';

    public $startDate = '';
    public $endDate = '';

    public function mount()
    {
        $this->filterYear = Carbon::now()->format('Y');
        $this->filterMonthYear = Carbon::now()->format('m.Y');
        $this->filterExecutor = (Auth::user()->id == 2190) ? '' : Auth::user()->userData->last_name . ' ' . Auth::user()->userData->first_name . ' ' . Auth::user()->userData->middle_name;
    }

    public function render()
    {
        $years = ModelsOperatingActivity::select(DB::raw("DATE_FORMAT(created_at, '%Y') as year"))->distinct()->get();
        $monthsYears = ModelsOperatingActivity::select(DB::raw("DATE_FORMAT(created_at, '%m.%Y') as monthYear"))
            ->where('created_at', 'like', $this->filterYear . '%')
            ->distinct()
            ->get();

        $weeksRange = $this->getWeeksRange($this->filterYear, $this->filterMonthYear);

        $listOfActivityDirectionsForUser = UserActivityDirection::join('activity_directions', 'user_activity_directions.activity_direction_id', '=', 'activity_directions.id')
            ->select('activity_directions.id', 'activity_directions.name')
            ->where('user_activity_directions.user_id', '=', Auth::user()->id)
            ->get();

        $listOfActivityDirections = UserActivityDirection::join('activity_directions', 'user_activity_directions.activity_direction_id', '=', 'activity_directions.id');

        if (!empty($this->filterExecutor)) {
            $listOfActivityDirections->join('users_data', 'user_activity_directions.user_id', '=', 'users_data.user_id')
                ->select('activity_directions.id', 'activity_directions.name', DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) AS fio"))
                ->having('fio', '=', $this->filterExecutor);
        } else {
            $listOfActivityDirections->select('activity_directions.id', 'activity_directions.name');
        }

        $listOfActivityDirections = $listOfActivityDirections->distinct()
            ->orderBy('activity_directions.name')
            ->get();

        $listOfExecutors = ModelsOperatingActivity::join('users_data', 'operating_activities.user_id', '=', 'users_data.user_id')
            ->select(
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name'
            );

        if (!empty($this->filterActivityDirection)) {
            $listOfExecutors->where('operating_activities.activity_direction', '=', $this->filterActivityDirection);
        }

        $listOfExecutors = $listOfExecutors->distinct()->get();

        $operatingActivity = ModelsOperatingActivity::join('users_data', 'operating_activities.user_id', '=', 'users_data.user_id')
            ->select(
                'operating_activities.id',
                'operating_activities.activity_direction',
                'operating_activities.task',
                'operating_activities.description',
                'operating_activities.status',
                'operating_activities.file_path',
                'operating_activities.user_id',
                DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name) AS fio')
            );

        if (!empty($this->filterActivityDirection)) {
            $operatingActivity->where('operating_activities.activity_direction', '=', $this->filterActivityDirection);
        }

        if (!empty($this->filterExecutor)) {
            $operatingActivity->where(DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name)'), 'like', '%' . $this->filterExecutor . '%');
        }

        if (!empty($this->filterStatus)) {
            $operatingActivity->where('operating_activities.status', '=', $this->filterStatus);
        }

        if (!empty($this->week) && $this->week != 0) {
            $now = Carbon::parse($this->week);
            $weekStartDate = $now->startOfWeek()->format('Y-m-d');
            $weekEndDate = $now->endOfWeek()->addDay()->format('Y-m-d');

            $operatingActivity
                ->where('operating_activities.created_at', '>=', Carbon::parse($weekStartDate)->format('Y-m-d'))
                ->where('operating_activities.created_at', '<=', Carbon::parse($weekEndDate)->format('Y-m-d'));
        }

        $operatingActivity = $operatingActivity
            ->orderBy('id')
            ->orderBy('fio')
            ->paginate(50);

        return view('livewire.operating-activity', [
            'operatingActivity' => $operatingActivity,
            'years' => $years,
            'monthsYears' => $monthsYears,
            'weeksRange' => $weeksRange,
            'listOfActivityDirections' => $listOfActivityDirections,
            'listOfActivityDirectionsForUser' => $listOfActivityDirectionsForUser,
            'listOfExecutors' => $listOfExecutors,
        ]);
    }

    public function callMethod($methodName, $param = null)
    {
        $this->{$methodName}($param);
    }

    public function creatingAndEditingModalFormVisible($isEdit, $id = 0, $userId = 0): void
    {
        $this->resetFields();

        $this->isEdit = $isEdit;

        if ($this->isEdit) {
            $this->edit($id, $userId);
        } else {
            $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
            $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
        }

        $this->isCreatingAndEditingModalFormVisible = true;
    }

    public function descriptionModalFormVisible($id): void
    {
        $operatingActivity = ModelsOperatingActivity::findOrFail($id);
        $this->description = $operatingActivity->description;

        $operatingActivityFile = OperatingActivityFile::where('operating_activity_id', '=', $operatingActivity->id)->get();
        $this->userFiles = $operatingActivityFile;

        $this->isDescriptionModalFormVisible = true;
    }

    public function alertVisible(): void
    {
        $this->isAlertVisible = false;
    }

    public function confirmDeleteRecordModalVisible($id, $userId)
    {
        $this->id = $id;
        $this->userId = $userId;

        $this->isConfirmDeleteRecordModalVisible = true;
    }

    public function filterModalFormVisible(): void
    {
        $this->isFilterModalFormVisible = true;
    }

    public function setFilterYear(): void
    {
        $this->filterYear = $this->filterYear;
        $this->filterMonthYear = '';
    }

    public function setFilterMonthYear(): void
    {
        $this->filterMonthYear = $this->filterMonthYear;
    }

    public function setFilterActivityDirection(): void
    {
        $this->filterActivityDirection = $this->filterActivityDirection;
    }

    public function setFilterExecutor(): void
    {
        $this->filterExecutor = $this->filterExecutor;
    }

    public function setFilterStatus(): void
    {
        $this->filterStatus = $this->filterStatus;
    }

    public function store(): void
    {
        if (!empty($this->files)) {
            $this->validate([
                'activityDirection' => 'required|string|max:255',
                'task' => 'required|string|max:2500',
                'description' => 'string|max:3500',
                'status' => 'required|numeric|min:1',
                'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png|max:3072'
            ]);
        } else {
            $this->validate([
                'activityDirection' => 'required|string|max:255',
                'task' => 'required|string|max:2500',
                'description' => 'string|max:3500',
                'status' => 'required|numeric|min:1'
            ]);
        }

        $operatingActivity = ModelsOperatingActivity::create([
            'activity_direction' => $this->activityDirection,
            'task' => $this->task,
            'description' =>  $this->description,
            'status' => $this->status,
            'file_path' => null,
            'user_id' => Auth::user()->id,
            'created_at' => Carbon::parse($this->endDate)->format('Y-m-d')
        ]);

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

        $this->isCreatingAndEditingModalFormVisible = false;
        $this->resetFields();

        session()->flash('type', 'success');
        session()->flash('message', 'Запись успешно добавлена!');
    }

    public function removeFileFromArray($index)
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files); // Переиндексация массива
    }

    public function removeFile($id)
    {
        $operatingActivityFile = OperatingActivityFile::find($id);
        unlink(storage_path('app/public/' . $operatingActivityFile->file_path));
        $operatingActivityFile->delete();

        $this->userFiles = OperatingActivityFile::where('operating_activity_id', '=', $this->id)->get();
    }

    public function edit($id, $userId): void
    {
        $this->id = $id;
        $this->userId = $userId;

        $operatingActivity = ModelsOperatingActivity::findOrFail($this->id);
        $this->activityDirection = $operatingActivity->activity_direction;
        $this->task = $operatingActivity->task;
        $this->description = $operatingActivity->description;
        $this->status = $operatingActivity->status;
        $this->file_path = $operatingActivity->file_path;
        $weekRange = Carbon::parse($operatingActivity->created_at);
        $this->startDate = $weekRange->startOfWeek()->format('Y-m-d');
        $this->endDate = $weekRange->endOfWeek()->format('Y-m-d');

        $operatingActivityFile = OperatingActivityFile::where('operating_activity_id', '=', $operatingActivity->id)->get();

        if (!is_null($operatingActivityFile)) {
            $this->userFiles = $operatingActivityFile;
        }
    }

    public function update(): void
    {
        if (Auth::user()->id == $this->userId) {
            if (!empty($this->files)) {
                $this->validate([
                    'activityDirection' => 'required|string|max:255',
                    'task' => 'required|string|max:2500',
                    'description' => 'string|max:3500',
                    'status' => 'required|numeric|min:1',
                    'files.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,bmp,png|max:3072'
                ]);
            } else {
                $this->validate([
                    'activityDirection' => 'required|string|max:255',
                    'task' => 'required|string|max:2500',
                    'description' => 'string|max:3500',
                    'status' => 'required|numeric|min:1'
                ]);
            }

            $operatingActivity = ModelsOperatingActivity::findOrFail($this->id);
            $operatingActivity->activity_direction = $this->activityDirection;
            $operatingActivity->task = $this->task;
            $operatingActivity->description = $this->description;
            $operatingActivity->status = $this->status;
            $operatingActivity->created_at = Carbon::parse($this->endDate)->format('Y-m-d');
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

            $this->isCreatingAndEditingModalFormVisible = false;
            $this->resetFields();

            session()->flash('type', 'success');
            session()->flash('message', 'Запись успешно отредактирована!');
        } else {
            $this->isCreatingAndEditingModalFormVisible = false;

            session()->flash('type', 'error');
            session()->flash('message', 'Ошибка редактирования записи! У вас нет прав на редактирование записи!');
        }
    }

    public function destroy(): void
    {
        if (Auth::user()->id == $this->userId) {
            $operatingActivity = ModelsOperatingActivity::findOrFail($this->id);

            if (!is_null($operatingActivity->file_path)) {
                unlink(storage_path('app/public/' . $operatingActivity->file_path));
            }

            $operatingActivity->delete();

            $this->isConfirmDeleteRecordModalVisible = false;

            session()->flash('type', 'success');
            session()->flash('message', 'Запись успешно удалена!');
        } else {
            $this->isConfirmDeleteRecordModalVisible = false;

            session()->flash('type', 'error');
            session()->flash('message', 'Ошибка удаления записи! У вас нет прав на удаление записи!');
        }
    }

    public function confirmActivity($id): void
    {
        $operatingActivity = ModelsOperatingActivity::findOrFail($id);
        $operatingActivity->status = 3;
        $operatingActivity->update();

        session()->flash('type', 'success');
        session()->flash('message', 'Статус записи успешно изменен!');
    }

    public function resetFields(): void
    {
        $this->activityDirection = 0;
        $this->task = '';
        $this->description = '';
        $this->status = 0;
        $this->file_path = '';
        $this->files = [];
        $this->userFiles = [];

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function getWeeksRange($year, $monthYear): array
    {
        $weeksRange = array();

        if (!empty($monthYear)) {
            $dates = ModelsOperatingActivity::select(DB::raw("DATE_FORMAT(created_at, '%d.%m.%Y') as date"))
                ->where('created_at', 'like', $year . '%')
                ->where('created_at', 'like', Carbon::createFromFormat('m.Y', $monthYear)->format('Y-m') . '%')
                ->distinct()
                ->get()
                ->toArray();
        } else {
            $dates = ModelsOperatingActivity::select(DB::raw("DATE_FORMAT(created_at, '%d.%m.%Y') as date"))
                ->where('created_at', 'like', $year . '%')
                ->distinct()
                ->get()
                ->toArray();
        }

        foreach ($dates as $value) {
            $now = Carbon::parse($value['date']);
            $weekStartDate = $now->startOfWeek()->format('Y-m-d');
            $weekEndDate = $now->endOfWeek()->format('Y-m-d');

            $week = Carbon::parse($weekStartDate)->format('d.m.Y') . '-' . Carbon::parse($weekEndDate)->format('d.m.Y');

            if (!in_array($week, $weeksRange)) {
                array_push($weeksRange, $week);
            }
        }

        asort($weeksRange);

        return $weeksRange;
    }

    public function setFilterWeeks(): void
    {
        $this->week = $this->splitWeekRange();
    }

    public function splitWeekRange(): string
    {
        $data = explode('-', $this->filterWeeks);
        return $data[0];
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateOperatingActivityReport()
    {
        return redirect()->route('generate.operating.activity.report', [
            'week' =>  !empty($this->week) ? $this->week : 0,
            'fio' => !empty($this->filterExecutor) ? $this->filterExecutor : 0
        ]);
    }
}
