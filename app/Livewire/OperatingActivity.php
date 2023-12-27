<?php

namespace App\Livewire;

use App\Models\OperatingActivity as ModelsOperatingActivity;
use App\Models\UserActivityDirection;
use Carbon\Carbon;
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
    public $file = null;
    public $userId = 0;
    public $week = '';

    public string $filename = '';
    public int $rand = 1;

    public $isCreatingAndEditingModalFormVisible = false, $isDescriptionModalFormVisible = false, $isFilterModalFormVisible = false, $isAlertVisible = false, $isConfirmDeleteRecordModalVisible = false;
    public $isEdit = false;

    public $filterWeeks = '';
    public $filterActivityDirection = '';
    public $filterExecutor = '';
    public $filterStatus = '';

    public function mount()
    {
        $this->filterExecutor = (Auth::user()->id == 2190) ? '' : Auth::user()->userData->last_name . ' ' . Auth::user()->userData->first_name . ' ' . Auth::user()->userData->middle_name;
    }

    public function render()
    {
        $weeksRange = $this->getWeeksRange();

        $listOfActivityDirectionsForUser = UserActivityDirection::join('activity_directions', 'user_activity_directions.activity_direction_id', '=', 'activity_directions.id')
            ->select('activity_directions.id', 'activity_directions.name')
            ->where('user_activity_directions.user_id', '=', 24207)
            ->get();

        $listOfActivityDirections = UserActivityDirection::join('activity_directions', 'user_activity_directions.activity_direction_id', '=', 'activity_directions.id');

        if (!empty($this->filterExecutor)) {
            $listOfActivityDirections->join('users_data', 'user_activity_directions.user_id', '=', 'users_data.user_id')
                ->select('activity_directions.id', 'activity_directions.name', DB::raw("CONCAT(users_data.last_name, ' ', users_data.first_name, ' ', users_data.middle_name) AS fio"))
                ->having('fio', '=', $this->filterExecutor);
        }else {
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

        if (!empty($this->filterActivityDirection) && empty($this->filterExecutor) && empty($this->filterStatus)) {
            $operatingActivity->where('operating_activities.activity_direction', '=', $this->filterActivityDirection);
        }

        if (empty($this->filterActivityDirection) && !empty($this->filterExecutor) && empty($this->filterStatus)) {
            $operatingActivity->where(DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name)'), 'like', '%' . $this->filterExecutor . '%');
        }

        if (empty($this->filterActivityDirection) && empty($this->filterExecutor) && !empty($this->filterStatus)) {
            $operatingActivity->where('operating_activities.status', '=', $this->filterStatus);
        }

        if (!empty($this->filterActivityDirection) && !empty($this->filterExecutor) && empty($this->filterStatus)) {
            $operatingActivity->where('operating_activities.activity_direction', '=', $this->filterActivityDirection);
            $operatingActivity->where(DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name)'), 'like', '%' . $this->filterExecutor . '%');
        }

        if (empty($this->filterActivityDirection) && !empty($this->filterExecutor) && !empty($this->filterStatus)) {
            $operatingActivity->where(DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name)'), 'like', '%' . $this->filterExecutor . '%');
            $operatingActivity->where('operating_activities.status', '=', $this->filterStatus);
        }

        if (!empty($this->filterActivityDirection) && !empty($this->filterExecutor) && !empty($this->filterStatus)) {
            $operatingActivity->where('operating_activities.activity_direction', '=', $this->filterActivityDirection);
            $operatingActivity->where(DB::raw('CONCAT(users_data.last_name," ",users_data.first_name," ",users_data.middle_name)'), 'like', '%' . $this->filterExecutor . '%');
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
            'weeksRange' => $weeksRange,
            'listOfActivityDirections' => $listOfActivityDirections,
            'listOfActivityDirectionsForUser' => $listOfActivityDirectionsForUser,
            'listOfExecutors' => $listOfExecutors,
        ]);
    }

    public function creatingAndEditingModalFormVisible($isEdit, $id = 0, $userId = 0): void
    {
        $this->resetFields();

        $this->isEdit = $isEdit;

        if ($this->isEdit)
            $this->edit($id, $userId);

        $this->isCreatingAndEditingModalFormVisible = true;
    }

    public function descriptionModalFormVisible($id): void
    {
        $operatingActivity = ModelsOperatingActivity::findOrFail($id);
        $this->description = $operatingActivity->description;
        $this->file = $operatingActivity->file_path;

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
        if (!is_null($this->file)) {
            $this->validate([
                'activityDirection' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'status' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx,jpg,bmp,png|max:3072',
            ]);
        } else {
            $this->validate([
                'activityDirection' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'status' => 'required|numeric|min:1'
            ]);
        }

        $filePath = (!is_null($this->file)) ? $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public') : null;

        ModelsOperatingActivity::create([
            'activity_direction' => $this->activityDirection,
            'task' => $this->task,
            'description' =>  $this->description,
            'status' => $this->status,
            'file_path' =>  $filePath,
            'user_id' => Auth::user()->id
        ]);

        $this->isCreatingAndEditingModalFormVisible = false;
        $this->isAlertVisible = true;

        $this->resetFields();

        session()->flash('message-success', 'Запись успешно добавлена!');
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

        if (!is_null($operatingActivity->file_path)) {
            $data = explode('/', $operatingActivity->file_path);
            $this->filename = $data[2];
        }
    }

    public function update(): void
    {
        if (Auth::user()->id == $this->userId) {
            if (!is_null($this->file)) {
                $this->validate([
                    'activityDirection' => 'required|string|max:255',
                    'task' => 'required|string|max:1000',
                    'description' => 'string|max:1500',
                    'status' => 'required|numeric|min:1',
                    'file' => 'file|mimes:pdf,doc,docx,jpg,bmp,png|max:3072',
                ]);
            } else {
                $this->validate([
                    'activityDirection' => 'required|string|max:255',
                    'task' => 'required|string|max:1000',
                    'description' => 'string|max:1500',
                    'status' => 'required|numeric|min:1'
                ]);
            }

            $operatingActivity = ModelsOperatingActivity::findOrFail($this->id);
            $operatingActivity->activity_direction = $this->activityDirection;
            $operatingActivity->task = $this->task;
            $operatingActivity->description = $this->description;
            $operatingActivity->status = $this->status;

            if (!is_null($this->file)) {
                if (!is_null($operatingActivity->file_path)) {
                    unlink(storage_path('app/public/' . $operatingActivity->file_path));
                }

                $filePath = $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
                $operatingActivity->file_path = $filePath;
            }

            $operatingActivity->update();

            $this->isCreatingAndEditingModalFormVisible = false;
            $this->isAlertVisible = true;

            $this->resetFields();

            session()->flash('message-success', 'Запись успешно отредактирована!');
        } else {
            $this->isCreatingAndEditingModalFormVisible = false;
            $this->isAlertVisible = true;

            session()->flash('message-error', 'Ошибка редактирования записи! У вас нет прав на редактирование записи!');
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
            $this->isAlertVisible = true;

            session()->flash('message-success', 'Запись успешно удалена!');
        } else {
            $this->isConfirmDeleteRecordModalVisible = false;
            $this->isAlertVisible = true;

            session()->flash('message-error', 'Ошибка удаления записи! У вас нет прав на удаление записи!');
        }
    }

    public function confirmActivity($id): void
    {
        $operatingActivity = ModelsOperatingActivity::findOrFail($id);
        $operatingActivity->status = 3;
        $operatingActivity->update();

        $this->isAlertVisible = true;

        session()->flash('message-success', 'Статус записи успешно изменен!');
    }

    public function resetFields(): void
    {
        $this->activityDirection = 0;
        $this->task = '';
        $this->description = '';
        $this->status = 0;
        $this->file_path = '';
        $this->file = null;

        $this->rand++;

        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function getWeeksRange(): array
    {
        $weeksRange = array();

        $dates = ModelsOperatingActivity::select(DB::raw("DATE_FORMAT(created_at, '%d.%m.%Y') as date"))
            ->distinct()
            ->orderBy('date', 'ASC')
            ->get()
            ->toArray();

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
