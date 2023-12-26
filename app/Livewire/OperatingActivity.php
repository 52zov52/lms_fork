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
    public $activityDirection = '';
    public $task = '';
    public $description = '';
    public $status = 0;
    public $file = null;
    public $userId = 0;
    public $week = '';

    public $isCreatingAndEditingModalFormVisible = false, $isDescriptionModalFormVisible = false, $isFilterModalFormVisible = false;
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

        $listOfActivityDirections = UserActivityDirection::join('activity_directions', 'user_activity_directions.activity_direction_id', '=', 'activity_directions.id')
            ->select('activity_directions.id', 'activity_directions.name')
            ->distinct()
            ->orderBy('activity_directions.name')
            ->get();

        //$catologActivityDirections = ModelsOperatingActivity::select('activity_direction')->distinct()->get();

        $listOfExecutors = ModelsOperatingActivity::join('users_data', 'operating_activities.user_id', '=', 'users_data.user_id')
            ->select(
                'users_data.last_name',
                'users_data.first_name',
                'users_data.middle_name'
            )->distinct()->get();

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

        $filename = (!is_null($this->file)) ? $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public') : null;

        ModelsOperatingActivity::create([
            'activity_direction' => $this->activityDirection,
            'task' => $this->task,
            'description' =>  $this->description,
            'status' => $this->status,
            'file_path' =>  $filename,
            'user_id' => Auth::user()->id
        ]);

        $this->isCreatingAndEditingModalFormVisible = false;
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

                $filename = $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
                $operatingActivity->file_path = $filename;
            }

            $operatingActivity->update();

            $this->isCreatingAndEditingModalFormVisible = false;
            $this->resetFields();

            session()->flash('message-success', 'Запись успешно отредактирована!');
        } else {
            $this->isCreatingAndEditingModalFormVisible = false;
            session()->flash('message-error', 'Ошибка редактирования записи! У вас нет прав на редактирование записи!');
        }
    }

    public function destroy($id, $userId): void
    {
        $this->userId = $userId;

        if (Auth::user()->id == $this->userId) {
            $operatingActivity = ModelsOperatingActivity::findOrFail($id);
            $operatingActivity->delete();

            session()->flash('message-success', 'Запись успешно удалена!');
        } else {
            session()->flash('message-error', 'Ошибка удаления записи! У вас нет прав на удаление записи!');
        }
    }

    public function confirmActivity($id): void
    {
        $operatingActivity = ModelsOperatingActivity::findOrFail($id);
        $operatingActivity->status = 3;
        $operatingActivity->update();

        session()->flash('message-success', 'Запись успешно обновлена!');
    }

    public function resetFields(): void
    {
        $this->activityDirection = '';
        $this->task = '';
        $this->description = '';
        $this->status = 0;
        $this->file = null;
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
