<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="projectBoardId" wire:change="changeValue" name="project_doard_id" id="project-doard-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите из списка проект</option>
            @foreach($projectBoard as $project)
            <option value="{{ $project->id }}">{{ $project->board_name }}</option>
            @endforeach
        </select>
        <button wire:click="boardModalVisible" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">Добавить новый проект</button>
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!$projectTaskList->isEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-2" wire:sortable-group="updateTaskOrder">
            @foreach ($projectTaskList as $taskList)
            <div class="flex flex-col bg-gray-100 border border-t-4 board-border-{{ $taskList->color }}-500 rounded-xl">
                <div class="flex items-center justify-between space-x-1 h-14 bg-white rounded-t-xl px-4">
                    <div>
                        <span class="task-list-badge-bg-{{ $taskList->color }}-300 rounded-xl px-2 py-1 text-sm">{{ $taskList->task_list_name }}</span>
                        <span class="text-sm">{{ $taskList->task()->where('project_task_list_id', '=', $taskList->id)->count() }} шт.</span>
                    </div>
                    <button wire:click="taskModalVisible({{ $taskList->id }}, false)" class="bg-blue-500 hover:bg-blue-600 rounded-full px-2 py-1 text-sm text-white">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
                <div class="flex flex-col space-y-2 px-4 py-2" wire:sortable-group.item-group="{{ $taskList->id }}" wire:sortable-group.options="{ animation: 100 }">
                    @foreach ($taskList->task()->orderBy('order')->get() as $task)
                    <div class="flex flex-col space-y-2 w-auto bg-white rounded shadow-xl p-4" wire:sortable-group.item="{{ $task->id }}" wire:key="task-{{ $task->id }}">
                        <button class="text-right" wire:sortable-group.handle>
                            <i class="fa-solid fa-arrows-up-down-left-right"></i>
                        </button>
                        <span class="text-sm font-semibold break-normal">{{ $task->task_name }}</span>
                        <span class="text-xs">Выполнить до: {{ $task->due_date }}</span>
                        <div class="flex justify-end space-x-2">
                            <button wire:click="taskModalVisible({{ $task->id }}, true)" class="bg-sky-500 hover:bg-sky-600 rounded-md px-[0.5rem] py-1 text-xs text-white">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="deleteProjectTask({{ $task->id }})" class="bg-red-500 hover:bg-red-600 rounded-md px-[0.6rem] py-1 text-xs text-white">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует. Для отображения данных выберите проект из списка или создайте новый.
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog add board -->
    <x-dialog-modal wire:model="isBoardModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма создания нового проекта
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <input class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" wire:model="boardName" type="text" placeholder="Введите наименование проекта">
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isBoardModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="addProjectBoard" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Создать') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog add task -->
    <x-dialog-modal wire:model="isTaskModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                {{ (!$isEditTask) ? __('Форма создания новой задачи') : __('Форма редактирования задачи') }}
            </x-slot>

            <x-slot name="content">
                <div class="w-full">
                    <input class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" wire:model="taskName" type="text" placeholder="Введите наименование задачи">
                </div>
                <div class="w-full mt-2">
                    <input class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" wire:model="dueDate" type="text" placeholder="Введите срок исполнения (год-месяц-день)">
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isTaskModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="{{ (!$isEditTask) ? 'addProjectTask' : 'updateProjectTask' }}" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ (!$isEditTask) ? __('Создать') : __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>