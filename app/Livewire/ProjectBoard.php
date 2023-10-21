<?php

namespace App\Livewire;

use App\Models\ProjectBoard as ModelsProjectBoard;
use App\Models\ProjectTask;
use App\Models\ProjectTaskList;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProjectBoard extends Component
{
    public int $projectBoardId = 0, $projectTaskListId = 0, $projectTaskId = 0;
    public string $boardName = "", $taskListName = "", $color = "", $taskName = "", $dueDate = "";

    public bool $isBoardModalVisible = false, $isTaskModalVisible = false, $isEditTask = false;

    public function render()
    {
        $projectBoard = ModelsProjectBoard::where('user_id', '=', Auth::user()->id)->get();
        $projectTaskList = ProjectTaskList::where('project_board_id', '=', $this->projectBoardId)->get();

        return view('livewire.project-board', [
            'projectBoard' => $projectBoard,
            'projectTaskList' => $projectTaskList
        ]);
    }

    public function boardModalVisible(): void
    {
        $this->isBoardModalVisible = true;
    }

    public function taskModalVisible($id, $isEditTask): void
    {
        $this->isEditTask = $isEditTask;

        $this->isTaskModalVisible = true;

        if(!$this->isEditTask)
        {
            $this->projectTaskListId = $id;
        }
        else
        {
            $this->projectTaskId = $id;
            $this->editProjectTask();
        }   
    }

    public function changeValue()
    {
        $this->projectBoardId = $this->projectBoardId;
    }

    public function addProjectBoard(): void
    {
        $projectBoard = new ModelsProjectBoard();
        $projectBoard->board_name = $this->boardName;
        $projectBoard->user_id = Auth::user()->id;
        $projectBoard->save();

        $data = [
            ['title' => 'Надо сделать', 'color' => 'gray'],
            ['title' => 'В работе', 'color' => 'blue'],
            ['title' => 'Готово', 'color' => 'green'],
            ['title' => 'Ожидают', 'color' => 'orange'],
        ];

        foreach ($data as $key => $value) {
            ProjectTaskList::create([
                'task_list_name' => $value['title'],
                'due_date' => $value['color'],
                'project_board_id' => $projectBoard->id
            ]);
        }

        $this->isBoardModalVisible = false;

        $this->boardName = "";

        session()->flash('message-success', 'Проект успешно создан!');
    }

    public function addProjectTaskList(): void
    {
    }

    public function addProjectTask(): void
    {
        ProjectTask::create([
            'task_name' => $this->taskName,
            'due_date' => $this->dueDate,
            'project_task_list_id' => $this->projectTaskListId
        ]);

        $this->isTaskModalVisible = false;

        $this->taskName = "";
        $this->dueDate = "";

        session()->flash('message-success', 'Задача успешно добавлена в группу!');
    }

    public function editProjectTask(): void
    {
        $projectTask = ProjectTask::findOrFail($this->projectTaskId);
        $this->taskName = $projectTask->task_name;
        $this->dueDate = $projectTask->due_date;
    }

    public function updateProjectTask() : void
    {
        $projectTask = ProjectTask::findOrFail($this->projectTaskId);
        $projectTask->task_name = $this->taskName;
        $projectTask->due_date = $this->dueDate;
        $projectTask->update();

        $this->isTaskModalVisible = false;

        $this->taskName = "";
        $this->dueDate = "";

        session()->flash('message-success', 'Данные задачи успешно изменены!');
    }

    public function deleteProjectTask($id) : void
    {
        $projectTask = ProjectTask::findOrFail($id);
        $projectTask->delete();

        session()->flash('message-success', 'Задача успешно удалена!');
    }

    public function updateTaskOrder($data): void
    {
        foreach ($data as $taskList) {
            foreach ($taskList['items'] as $task) {
                ProjectTask::where('id', '=', $task['value'])->update(['order' => $task['order'], 'project_task_list_id' => $taskList['value']]);
            }
        }
    }
}
