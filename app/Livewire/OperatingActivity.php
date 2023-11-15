<?php

namespace App\Livewire;

use App\Models\OperatingActivity as ModelsOperatingActivity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class OperatingActivity extends Component
{
    use WithFileUploads;

    public $id = 0;
    public $activityDirection = '';
    public $task = '';
    public $description = '';
    public $status = 0;
    public $file = null;

    public $isCreatingAndEditingModalFormVisible = false;
    public $isEdit = false;

    public $fio = '';

    public function render()
    {
        $operatingActivity = ModelsOperatingActivity::join('users_data', 'operating_activities.user_id', '=', 'users_data.user_id')
        ->select(
            'operating_activities.id',
            'operating_activities.activity_direction',
            'operating_activities.task',
            'operating_activities.description',
            'operating_activities.status',
            'operating_activities.file_path',
            'users_data.last_name',
            'users_data.first_name',
            'users_data.middle_name'
        )
        ->orWhere('users_data.last_name', 'like', '%' . $this->fio . '%')
        ->simplePaginate(50);

        return view('livewire.operating-activity', ['operatingActivity' => $operatingActivity]);
    }

    public function creatingAndEditingModalFormVisible($isEdit, $id = 0): void
    {
        $this->isEdit = $isEdit;

        if ($this->isEdit)
            $this->edit($id);

        $this->isCreatingAndEditingModalFormVisible = true;
    }

    public function store(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'activityDirection' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'status' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx|max:3072',
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

    public function edit($id): void
    {
        $this->id = $id;

        $operatingActivity = ModelsOperatingActivity::findOrFail($this->id);
        $this->activityDirection = $operatingActivity->activity_direction;
        $this->task = $operatingActivity->task;
        $this->description = $operatingActivity->description;
        $this->status = $operatingActivity->status;
    }

    public function update(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'activityDirection' => 'required|string|max:255',
                'task' => 'required|string|max:1000',
                'description' => 'string|max:1500',
                'status' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx|max:3072',
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
    }

    public function destroy($id): void
    {
        $operatingActivity = ModelsOperatingActivity::findOrFail($id);
        $operatingActivity->delete();

        session()->flash('message-success', 'Запись успешно удалена!');
    }

    public function resetFields(): void 
    {
        $this->activityDirection = '';
        $this->task = '';
        $this->description = '';
        $this->status = 0;
        $this->file = null;
    }
}
