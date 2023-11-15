<?php

namespace App\Livewire;

use App\Models\CategoriesOperatingActivity;
use App\Models\OperatingActivity as ModelsOperatingActivity;
use App\Models\OperatingActivityTask;
use App\Models\OperatingActivityTaskList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class OperatingActivity extends Component
{
    use WithFileUploads;

    public $name, $categoryId;
    public $taskListId, $taskListName;
    public $taskId, $taskName, $dueDate, $description, $status, $order, $file;

    public $isOperatingActivityModalVisible = false, $isTaskListModalVisible = false, $isTaskModalVisible = false, $isEdit = false;

    public $selectedCategoryId = 0, $selectedActivityId = 0, $selectedUserId = 0;

    public $users = [];

    public function render()
    {
        $categoriesOperatingActivities = CategoriesOperatingActivity::get();
        $operatingActivities = ModelsOperatingActivity::where('categories_operating_activity_id', '=', $this->selectedCategoryId)->get();

        $operatingActivityTaskList = OperatingActivityTaskList::where('operating_activity_id', '=', $this->selectedActivityId)->get();

        $this->users = User::permission('look-project-board')
            ->join('users_data', 'users.id', '=', 'users_data.user_id')
            ->select('users_data.user_id', 'users_data.last_name', 'users_data.first_name', 'users_data.middle_name')
            ->get();

        return view('livewire.operating-activity', [
            'categoriesOperatingActivities' => $categoriesOperatingActivities,
            'operatingActivities' => $operatingActivities,
            'operatingActivityTaskLists' => $operatingActivityTaskList
        ]);
    }

    public function operatingActivityModalVisible($isEdit)
    {
        $this->isEdit = $isEdit;

        if ($this->selectedCategoryId != 0) {

            if ($isEdit) {
                $this->editOperatingActivity();
            } else {
                $this->isOperatingActivityModalVisible = true;
            }
        } else {
            $text = (!$isEdit) ? 'созданием' : 'редактированием';
            session()->flash('message-error', "Перед $text вида деятельности выберите категорию!");
        }
    }

    public function taskListModalVisible($isEdit, $id = 0)
    {
        $this->isEdit = $isEdit;

        if ($isEdit) {
            $this->editTaskList($id);
        } else {
            $this->isTaskListModalVisible = true;
        }
    }

    public function taskModalVisible($id, $isEdit)
    {
        $this->isEdit = $isEdit;

        if (!$isEdit) {
            $this->taskListId = $id;
        } else {
            $this->taskId = $id;
            $this->editTask();
        }

        $this->isTaskModalVisible = true;
    }

    public function changeCategory(): void
    {
        $this->selectedCategoryId = $this->selectedCategoryId;
        $this->selectedActivityId = 0;
    }

    public function changeActivity(): void
    {
        $this->selectedActivityId = $this->selectedActivityId;
    }

    public function addOperatingActivity(): void
    {
        $this->validate([
            'name' => 'required|max:255',
            'categoryId' => 'required|numeric|min:1'
        ]);

        ModelsOperatingActivity::create([
            'name' => $this->name,
            'categories_operating_activity_id' => $this->categoryId,
            'user_id' => Auth::user()->id
        ]);

        $this->isOperatingActivityModalVisible = false;

        session()->flash('message-success', 'Вид деятельности успешно создан!');
    }

    public function editOperatingActivity(): void
    {
        if ($this->selectedActivityId != 0) {
            $operatingActivity = ModelsOperatingActivity::findOrFail($this->selectedActivityId);
            $this->name = $operatingActivity->name;
            $this->categoryId = $operatingActivity->categories_operating_activity_id;

            $this->isOperatingActivityModalVisible = true;
        } else {
            session()->flash('message-error', 'Перед редактированием выберите вид деятельности из списка!');
        }
    }

    public function updateOperatingActivity(): void
    {
        $this->validate([
            'name' => 'required|max:255',
            'categoryId' => 'required|numeric|min:1'
        ]);

        $operatingActivity = ModelsOperatingActivity::findOrFail($this->selectedActivityId);
        $operatingActivity->name = $this->name;
        $operatingActivity->categories_operating_activity_id = $this->categoryId;
        $operatingActivity->update();

        $this->isOperatingActivityModalVisible = false;

        session()->flash('message-success', 'Вид деятельности успешно обновлен!');
    }

    public function deleteOperatingActivity(): void
    {
        if ($this->selectedActivityId != 0) {

            $operatingActivity = ModelsOperatingActivity::findOrFail($this->selectedActivityId);
            $operatingActivity->delete();

            $data = OperatingActivityTaskList::where('operating_activity_id', '=', $this->selectedActivityId)->get();

            foreach ($data as $value) {
                $operatingActivityTask = OperatingActivityTask::where('operating_activity_task_list_id', '=', $value->id);
                $operatingActivityTask->delete();
            }

            $operatingActivityTaskList = OperatingActivityTaskList::where('operating_activity_id', '=', $this->selectedActivityId);
            $operatingActivityTaskList->delete();

            session()->flash('message-success', 'Вид деятельности успешно удален!');
        } else {
            session()->flash('message-error', 'Перед удалением выберите вид деятельности из списка!');
        }
    }

    public function addTaskList(): void
    {
        $this->validate([
            'taskListName' => 'required|max:33',
            'selectedActivityId' => 'required|numeric|min:1'
        ]);

        OperatingActivityTaskList::create([
            'task_list_name' => $this->taskListName,
            'operating_activity_id' => $this->selectedActivityId,
            'user_id' => ($this->selectedUserId == 0) ? Auth::user()->id : $this->selectedUserId
        ]);

        $this->isTaskListModalVisible = false;

        session()->flash('message-success', 'Группа успешно создана!');
    }

    public function editTaskList($id): void
    {
        $this->taskListId = $id;

        $operatingActivityTaskList = OperatingActivityTaskList::findOrfail($this->taskListId);
        $this->taskListName = $operatingActivityTaskList->task_list_name;
        $this->selectedUserId = $operatingActivityTaskList->user_id;

        $this->isTaskListModalVisible = true;
    }

    public function updateTaskList(): void
    {
        $this->validate([
            'taskListName' => 'required|max:33',
            'selectedActivityId' => 'required|numeric|min:1'
        ]);

        $operatingActivityTaskList = OperatingActivityTaskList::findOrfail($this->taskListId);
        $operatingActivityTaskList->task_list_name = $this->taskListName;
        $operatingActivityTaskList->user_id = ($this->selectedUserId == 0) ? Auth::user()->id : $this->selectedUserId;
        $operatingActivityTaskList->update();

        $this->isTaskListModalVisible = false;

        session()->flash('message-success', 'Группа успешно обновлена!');
    }

    public function deleteTaskList($id): void
    {
        $operatingActivityTaskList = OperatingActivityTaskList::findOrfail($id);
        $operatingActivityTaskList->delete();

        session()->flash('message-success', 'Группа успешно удалена!');
    }

    public function addTask(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'taskName' => 'required|max:150',
                'dueDate' => 'required',
                'status' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx|max:3072', // 3MB Max
            ]);
        } else {
            $this->validate([
                'taskName' => 'required|max:150',
                'dueDate' => 'required',
                'status' => 'required|numeric|min:1'
            ]);
        }

        $filename = (!is_null($this->file)) ? $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public') : null;

        OperatingActivityTask::create([
            'task_name' => $this->taskName,
            'due_date' => $this->dueDate,
            'description' => $this->description,
            'status' => $this->status,
            'file_path' => $filename,
            'operating_activity_task_list_id' => $this->taskListId,
            'user_id' => ($this->selectedUserId == 0) ? Auth::user()->id : $this->selectedUserId
        ]);

        $this->isTaskModalVisible = false;

        session()->flash('message-success', 'задача успешно создана!');
    }

    public function editTask(): void
    {
        $operatingActivityTask = OperatingActivityTask::findOrFail($this->taskId);
        $this->taskName = $operatingActivityTask->task_name;
        $this->dueDate = $operatingActivityTask->due_date;
        $this->description = $operatingActivityTask->description;
        $this->status = $operatingActivityTask->status;
        $this->selectedUserId = $operatingActivityTask->user_id;

        $this->isTaskModalVisible = true;
    }

    public function updateTask(): void
    {
        if (!is_null($this->file)) {
            $this->validate([
                'taskName' => 'required|max:150',
                'dueDate' => 'required',
                'status' => 'required|numeric|min:1',
                'file' => 'file|mimes:pdf,doc,docx|max:3072', // 3MB Max
            ]);
        } else {
            $this->validate([
                'taskName' => 'required|max:150',
                'dueDate' => 'required',
                'status' => 'required|numeric|min:1'
            ]);
        }

        $operatingActivityTask = OperatingActivityTask::findOrFail($this->taskId);
        $operatingActivityTask->task_name = $this->taskName;
        $operatingActivityTask->due_date = $this->dueDate;
        $operatingActivityTask->description = $this->description;
        $operatingActivityTask->status = $this->status;
        $operatingActivityTask->user_id = ($this->selectedUserId == 0) ? Auth::user()->id : $this->selectedUserId;

        if (!is_null($this->file)) { 
            if(!is_null($operatingActivityTask->file_path)){
                unlink(storage_path('app/public/' . $operatingActivityTask->file_path));
            }

            $filename = $this->file->store('files' . '/' . Carbon::now()->format('Y-m-d'), 'public');
            $operatingActivityTask->file_path = $filename;
        }

        $operatingActivityTask->update();

        $this->isTaskModalVisible = false;

        session()->flash('message-success', 'Задача успешно обновлена!');
    }

    public function deleteTask($id): void
    {
        $operatingActivityTask = OperatingActivityTask::findOrFail($id);
        $operatingActivityTask->delete();

        session()->flash('message-success', 'Задача успешно удалена!');
    }
}
