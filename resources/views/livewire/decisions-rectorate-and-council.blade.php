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
        @can('decisions-rectorate-and-council')
        <button wire:click="creatingAndEditingModalFormVisible(false)" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">
            Добавить запись
        </button>
        @endcan
        <button wire:click="filterModalFormVisible" class="block xl:hidden w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">
            Фильтры
        </button>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="hidden xl:block">
            <table class="w-full bg-white border shadow-md text-xs md:text-sm">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-64 border px-2 py-2 font-semibold">Должность</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Поручение</td>
                        <td class="w-40 border px-2 py-2 font-semibold">Срок исполнения</td>
                        <td class="w-40 border px-2 py-2 text-center font-semibold">Статус</td>
                        <td class="w-36 border px-2 py-2 text-center font-semibold">Действия</td>
                    </tr>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2"></td>
                        <td class="w-64 border px-2">
                            <select wire:model="filterPosition" name="filterPosition" wire:change="setFilterPosition" id="filter-position-id" class="w-64 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Все должности</option>
                                @foreach($positions as $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-auto border px-2"></td>
                        <td class="w-40 border px-2 py-1">
                            <select wire:model="filterDueDate" name="filterDueDate" wire:change="setFilterDueDate" id="filter-due-date-id" class="w-40 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Все сроки исполнения</option>
                                @foreach($catologDueDates as $catologDueDate)
                                <option value="{{ $catologDueDate->due_date }}">{{ $catologDueDate->due_date }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-40 border px-2">
                            <select wire:model="filterStatus" wire:change="setFilterStatus" name="filterStatus" id="filter-status-id" class="w-40 border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Все статусы</option>
                                <option value="1">В работе</option>
                                <option value="2">На утверждение</option>
                                <option value="3">Выполнено</option>
                            </select>
                        <td class="w-auto border px-2"></td>
                    </tr>
                    @foreach($decisionsRectorateAndCouncil as $value)
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-64 border px-2 py-2">{{ $value->position }}</td>
                        <td class="w-auto border px-2 py-2">{{ $value->task }}</td>
                        <td class="w-40 border px-2 py-2">{{ $value->due_date }}</td>
                        <td class="w-40 border px-2 py-2 text-center">
                            @if($value->status == 1)
                            <span class="bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В работе</span>
                            @elseif($value->status == 2)
                            <span class="bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">На утверждение</span>
                            @else
                            <span class="bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Выполнено</span>
                            @endif
                        </td>
                        <td class="w-36 border px-2 py-2 text-center">
                            @can('confirm-decisions-rectorate-and-council')
                            <button wire:click="confirmDecisions({{ $value->id }})" class="bg-green-500 hover:bg-green-600 rounded px-2 py-1.5 text-xs text-white" title="Подтверждение">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            @endcan
                            <button wire:click="descriptionModalFormVisible({{ $value->id }})" class="bg-indigo-500 hover:bg-indigo-600 rounded px-2 py-1.5 text-xs text-white" title="Доп. информация">
                                <i class="fa-solid fa-message"></i>
                            </button>
                            @can('decisions-rectorate-and-council')
                            <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }})" class="bg-sky-500 hover:bg-sky-600 rounded px-2 py-1.5 text-xs text-white" title="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="destroy({{ $value->id }})" class="bg-red-500 hover:bg-red-600 rounded px-2 py-1.5 text-xs text-white" title="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="block xl:hidden flex flex-col space-y-2">
            @foreach($decisionsRectorateAndCouncil as $value)
            <div class="flex flex-col space-y-2 border shadow-md p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Должность:</span>
                    <span>{{ $value->position }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Поручение:</span>
                    <span>{{ $value->task }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Срок исполнения:</span>
                    <span>{{ $value->due_date }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Статус:
                        @if($value->status == 1)
                        <span class="bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В работе</span>
                        @elseif($value->status == 2)
                        <span class="bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">На утверждение</span>
                        @else
                        <span class="bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Выполнено</span>
                        @endif
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="flex flex-row space-x-2">
                        @can('confirm-decisions-rectorate-and-council')
                        <button wire:click="confirmDecisions({{ $value->id }})" title="Подтверждение" class="w-auto grow bg-green-500 hover:bg-green-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        @endcan
                        <button wire:click="descriptionModalFormVisible({{ $value->id }})" title="Доп. информация" class="w-auto grow bg-indigo-500 hover:bg-indigo-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-message"></i>
                        </button>
                        @can('decisions-rectorate-and-council')
                        <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }})" title="Редактировать" class="w-auto grow bg-sky-500 hover:bg-sky-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button wire:click="destroy({{ $value->id }})" title="Удалить" class="w-auto grow bg-red-500 hover:bg-red-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        @endcan
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-2">
            {{ $decisionsRectorateAndCouncil->links() }}
        </div>
    </div>

    <!-- Creating And Editing Modal Form -->
    <x-dialog-modal wire:model="isCreatingAndEditingModalFormVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Форма создания новой записи') : __('Форма редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="position-id">Должность</label>
                        <select wire:model="position" name="position" id="position-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите должность</option>
                            @foreach($positions as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('position') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="task-id">Задача</label>
                        <input wire:model="task" type="text" name="task" id="task-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('task') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="due-date-id">Срок исполнения</label>
                        <input wire:model="dueDate" type="text" name="dueDate" id="due-date-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('dueDate') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description-id">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col">
                        <label for="status-id"></label>
                        <select wire:model="status" name="status" id="status-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите статус</option>
                            <option value="1">В работе</option>
                            <option value="2">На утверждение</option>
                            <option value="3">Выполнено</option>
                        </select>
                        @error('status') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <input wire:model="file" type="file" name="file" id="file-id" class="w-full">
                        @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="file">Идет загрузка файла ...</div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isCreatingAndEditingModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="{{ (!$isEdit) ? 'store' : 'update' }}" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Создать') : __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Description Modal Form -->
    <x-dialog-modal wire:model="isDescriptionModalFormVisible">
        <form>
            @csrf
            <x-slot name="title">
                {{ __('Дополнительная информация по записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="description-id">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" readonly></textarea>
                    </div>
                    @if(!is_null($file))
                    <div class="flex space-x-2">
                        <span class="font-semibold">Файл: </span>
                        <a href="storage/{{ $file }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">cкачать</a>
                    </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isDescriptionModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Filter Modal Form For Mobile Device -->
    <x-dialog-modal wire:model="isFilterModalFormVisible">
        <form>
            @csrf
            <x-slot name="title">
                {{ __('Фильтры') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <select wire:model="filterPosition" name="filterPosition" wire:change="setFilterPosition" id="filter-position-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Все должности</option>
                            @foreach($positions as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <select wire:model="filterDueDate" name="filterDueDate" wire:change="setFilterDueDate" id="filter-due-date-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Все сроки исполнения</option>
                            @foreach($catologDueDates as $catologDueDate)
                            <option value="{{ $catologDueDate->due_date }}">{{ $catologDueDate->due_date }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <select wire:model="filterStatus" wire:change="setFilterStatus" name="filterStatus" id="filter-status-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Все статусы</option>
                            <option value="1">В работе</option>
                            <option value="2">На утверждение</option>
                            <option value="3">Выполнено</option>
                        </select>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isFilterModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>