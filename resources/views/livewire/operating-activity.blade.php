<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    @if(session()->has('message-error'))
    <div class="flex items-center bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-error') }}
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 items-center">
        <select wire:model="selectedCategoryId" wire:change="changeCategory" name="categories_operating_activity_id" id="categories-operating-activity-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите категорию из списка</option>
            @foreach($categoriesOperatingActivities as $category)
            <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <select wire:model="selectedActivityId" wire:change="changeActivity" name="operating_activity_id" id="operating-activity-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите вид деятельности из списка</option>
            @foreach($operatingActivities as $activity)
            <option value="{{ $activity->id }}">{{ $activity->name }}</option>
            @endforeach
        </select>
        <button wire:click="operatingActivityModalVisible(false)" class="w-full h-8 md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-sm text-white">
            <i class="fa-solid fa-plus"></i>
        </button>
        <button wire:click="operatingActivityModalVisible(true)" class="w-full h-8 md:w-auto bg-sky-500 hover:bg-sky-600 rounded px-2 py-1 text-sm text-white">
            <i class="fa-solid fa-pen"></i>
        </button>
        <button wire:click="deleteOperatingActivity" class="w-full h-8 md:w-auto bg-red-500 hover:bg-red-600 rounded px-2 py-1 text-sm text-white">
            <i class="fa-solid fa-trash-can"></i>
        </button>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="w-full flex flex-col space-y-4">
            @if($selectedActivityId != 0)
            <div class="flex justify-end">
                <button wire:click="taskListModalVisible(false)" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-sm text-white">Добавить группу</button>
            </div>
            @endif
            @if(!$operatingActivityTaskLists->isEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-2">
                @foreach ($operatingActivityTaskLists as $taskList)
                <div class="flex flex-col bg-gray-100 border border-blue-500 border-t-4 border-t-blue-500 rounded-xl">
                    <div class="relative flex items-center justify-between space-x-1 h-14 bg-white rounded-t-xl px-2">
                        <span class="text-sm">{{ $taskList->task_list_name }}</span>
                        <span class="text-sm">{{ $taskList->task()->where('operating_activity_task_list_id', '=', $taskList->id)->count() }} шт.</span>
                        <div class="absolute -bottom-3 right-2 flex space-x-1">
                            <button wire:click="taskModalVisible({{ $taskList->id }}, false)" class="bg-blue-500 hover:bg-blue-600 rounded-full px-2 py-1 text-sm text-white">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                            <button wire:click="taskListModalVisible(true, {{ $taskList->id }})" class="bg-sky-500 hover:bg-sky-600 rounded-full px-2 py-1 text-sm text-white">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="deleteTaskList({{ $taskList->id }})" class="bg-red-500 hover:bg-red-600 rounded-full px-2 py-1 text-sm text-white">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                    <div class="flex flex-col space-y-2 px-4 py-4">
                        @foreach ($taskList->task()->orderBy('order')->get() as $task)
                        <div class="flex flex-col space-y-2 w-auto bg-white border {{ ($task->status == 1) ? 'border-orange-500' : 'border-green-500' }} rounded shadow-xl p-4">
                            <span class="text-center text-sm font-semibold break-normal">{{ $task->task_name }}</span>
                            <span class="text-sm break-normal">{{ $task->description }}</span>
                            @if(!is_null($task->file_path))
                            <span class="text-sm break-normal"><strong>Файл:</strong> <a href="storage/{{ $task->file_path }}" class="text-blue-500 hover:underline" target="_blank" rel="noopener noreferrer">скачать</a></span>
                            @endif
                            <span class="text-sm"><strong>Выполнить до:</strong> {{ $task->due_date }}</span>
                            <div class="flex justify-between space-x-2">
                                <div>
                                    @if($task->status == 1)
                                    <span class="bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В работе</span>
                                    @else
                                    <span class="bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Исполнено</span>
                                    @endif
                                </div>
                                <div>
                                    <button wire:click="taskModalVisible({{ $task->id }}, true)" class="bg-sky-500 hover:bg-sky-600 rounded-md px-[0.5rem] py-1 text-xs text-white">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button wire:click="deleteTask({{ $task->id }})" class="bg-red-500 hover:bg-red-600 rounded-md px-[0.6rem] py-1 text-xs text-white">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>

                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует. Для отображения данных выберите категорию и вид деятельности из списка или создайте новый.
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog add -->
    <x-dialog-modal wire:model="isOperatingActivityModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма {{ (!$isEdit) ? 'создания' : 'редактирования' }} вида деятельности
            </x-slot>

            <x-slot name="content">
                <div class="w-full flex flex-col space-y-2">
                    <div class="w-full">
                        <input wire:model="name" type="text" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Введите наименование проекта">
                        @error('name')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="w-full">
                        <select wire:model="categoryId" name="categories_operating_activity_id" id="categories-operating-activity-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите категорию из списка</option>
                            @foreach($categoriesOperatingActivities as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('categoryId')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isOperatingActivityModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                @if(!$isEdit)
                <button wire:click.prevent="addOperatingActivity" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Создать') }}
                </button>
                @else
                <button wire:click.prevent="updateOperatingActivity" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Обновить') }}
                </button>
                @endif
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog task list-->
    <x-dialog-modal wire:model="isTaskListModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма {{ (!$isEdit) ? 'создания' : 'редактирования' }} группы
            </x-slot>

            <x-slot name="content">
                <div class="w-full flex flex-col space-y-2">
                    <div class="w-full">
                        <input wire:model="taskListName" type="text" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Введите наименование группы">
                        @error('taskListName')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="w-full">
                        <select wire:model="selectedUserId" name="user_id" id="user-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите пользователя из списка</option>
                            @foreach($users as $user)
                            <option value="{{ $user->user_id }}">{{ $user->last_name }} {{ $user->first_name }} {{ $user->middle_name }}</option>
                            @endforeach
                        </select>
                        @error('selectedUserId')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isTaskListModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                @if(!$isEdit)
                <button wire:click.prevent="addTaskList" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Создать') }}
                </button>
                @else
                <button wire:click.prevent="updateTaskList" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Обновить') }}
                </button>
                @endif
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog add task -->
    <x-dialog-modal wire:model="isTaskModalVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                Форма {{ (!$isEdit) ? 'создания' : 'редактирования' }} задачи
            </x-slot>

            <x-slot name="content">
                <div class="w-full flex flex-col space-y-2">
                    <div class="w-full">
                        <input wire:model="taskName" type="text" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Введите наименование задачи">
                        @error('taskName')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="w-full">
                        <input wire:model="dueDate" type="text" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Введите срок исполнения задачи">
                        @error('dueDate')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="w-full">
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ $description }}</textarea>
                        @error('description')<span class="text-red-500">{{ $message }}</span>@enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="w-full">
                            <select wire:model="status" name="status_id" id="status-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Выберите статус из списка</option>
                                <option value="1">В работе</option>
                                <option value="2">Исполнено</option>
                            </select>
                            @error('status')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                        <div class="w-full">
                            <select wire:model="selectedUserId" name="user_id" id="user-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Выберите пользователя из списка</option>
                                @foreach($users as $user)
                                <option value="{{ $user->user_id }}">{{ $user->last_name }} {{ $user->first_name }} {{ $user->middle_name }}</option>
                                @endforeach
                            </select>
                            @error('selectedUserId')<span class="text-red-500">{{ $message }}</span>@enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <input wire:model="file" type="file" name="file" id="file-id" class="w-full">
                        @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="file">Идет загрузка файла ...</div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isTaskModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                @if(!$isEdit)
                <button wire:click.prevent="addTask" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Создать') }}
                </button>
                @else
                <button wire:click.prevent="updateTask" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ __('Обновить') }}
                </button>
                @endif
            </x-slot>
        </form>
    </x-dialog-modal>
</div>