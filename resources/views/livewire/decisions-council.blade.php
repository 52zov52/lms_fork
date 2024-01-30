<div class="flex flex-col space-y-2">

    @if(session()->has('message-success') && $isAlertVisible)
    <div class="flex items-center justify-between bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-circle-check fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                {{ session('message-success') }}
            </div>
        </div>
        <button wire:click="alertVisible"><i class="fa-solid fa-xmark fal-lg"></i></button>
    </div>
    @endif

    @if(session()->has('message-error') && $isAlertVisible)
    <div class="flex items-center justify-between bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-xmark fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                {{ session('message-error') }}
            </div>
        </div>
        <button wire:click="alertVisible"><i class="fa-solid fa-xmark fal-lg"></i></button>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 items-center">
        @can('create-edit-delete-decisions-rectorate-and-council')
        <button wire:click="creatingAndEditingModalFormVisible(false)" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить запись
        </button>
        @endcan
        <button wire:click="filterModalFormVisible" class="block xl:hidden w-full md:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-filter"></i>
            Фильтры
        </button>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="hidden xl:block">
            <table class="w-full bg-white border shadow-md text-xs">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-64 border px-2 py-2 font-semibold">Должность</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Поручение</td>
                        <td class="w-44 border px-2 py-2 font-semibold">Срок исполнения</td>
                        <td class="w-44 border px-2 py-2 text-center font-semibold">Дата и номер решения</td>
                        @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
                        <td class="w-24 border px-2 py-2 text-center font-semibold">Действия</td>
                        @endcan
                    </tr>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2"></td>
                        <td class="w-64 border px-2">
                            <select wire:model="filterPosition" name="filterPosition" wire:change="setFilterPosition" id="filter-position-id" class="w-64 border border-gray-300 rounded text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Все должности</option>
                                @foreach($positions as $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-auto border px-2"></td>
                        <td class="w-44 border px-2 py-1">
                            <select wire:model="filterDueDate" name="filterDueDate" wire:change="setFilterDueDate" id="filter-due-date-id" class="w-44 border border-gray-300 rounded text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Все сроки исполнения</option>
                                @foreach($catologDueDates as $catologDueDate)
                                <option value="{{ $catologDueDate->due_date }}">{{ $catologDueDate->due_date }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-44 border px-2">
                            <select wire:model="filterDateAndNumberDecision" name="filterDateAndNumberDecision" wire:change="setFilterDateAndNumberDecision" id="filter-date-and-number-decision-id" class="w-44 border border-gray-300 rounded text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Все даты и номера решения</option>
                                @foreach($catologDateAndNumberDecisions as $catologDateAndNumberDecision)
                                <option value="{{ $catologDateAndNumberDecision->date_and_number_decision }}">{{ $catologDateAndNumberDecision->date_and_number_decision }}</option>
                                @endforeach
                            </select>
                        </td>
                        @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
                        <td class="w-24 border px-2"></td>
                        @endcan
                    </tr>
                    @foreach($decisionsRectorateAndCouncil as $value)
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-64 border px-2 py-2">{{ $value->position }}</td>
                        <td class="w-auto border px-2 py-2">{{ $value->task }}</td>
                        <td class="w-44 border px-2 py-2">{{ $value->due_date }}</td>
                        <td class="w-44 border px-2 py-2 text-center">{{ $value->date_and_number_decision }}</td>
                        @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
                        <td class="w-24 border px-2 py-2 text-center">
                            @can('confirm-decisions-rectorate-and-council')
                            <button wire:click="confirmDecisions({{ $value->id }})" class="bg-green-500 hover:bg-green-600 rounded px-2 py-1.5 text-xs text-white" title="Подтверждение">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            @endcan
                            @can('create-edit-delete-decisions-rectorate-and-council')
                            <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }})" class="bg-sky-500 hover:bg-sky-600 rounded px-2 py-1.5 text-xs text-white" title="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }})" class="bg-red-500 hover:bg-red-600 rounded px-2 py-1.5 text-xs text-white" title="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endcan
                        </td>
                        @endcan
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
                    <span class="font-semibold">Дата и номер решения:</span>
                    <span>{{ $value->date_and_number_decision }}</span>
                </div>
                @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="flex flex-row space-x-2">
                        @can('confirm-decisions-rectorate-and-council')
                        <button wire:click="confirmDecisions({{ $value->id }})" class="w-auto grow bg-green-500 hover:bg-green-600 rounded px-2 py-1 text-white" title="Подтверждение">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        @endcan
                        @can('create-edit-delete-decisions-rectorate-and-council')
                        <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }})" title="Редактировать" class="w-auto grow bg-sky-500 hover:bg-sky-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }})" title="Удалить" class="w-auto grow bg-red-500 hover:bg-red-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        @endcan
                    </div>
                </div>
                @endcan
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="date-decision-id" class="font-semibold">Дата решения<span class="ml-1 text-red-500">*</span></label>
                            <div class="flex">
                                @if(!$isVisibleDateDecisionField)
                                <select wire:model="dateDecision" name="date_decision" id="date-decision-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="0">Выберите дату решения</option>
                                    @foreach($listOfDateDecision as $value)
                                    <option value="{{ $value->date_decision }}">
                                        {{ \Carbon\Carbon::parse($value->date_decision)->format('d.m.Y') }}
                                    </option>
                                    @endforeach
                                </select>
                                @else
                                <input wire:model="dateDecision" type="text" name="date_decision" id="date-decision-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Формат ввода ДД.ММ.ГГГГ">
                                @endif
                                <button wire:click="visibleDateDecisionField" class="ml-1 bg-blue-500 hover:bg-blue-600 rounded px-2.5 py-1.5 text-xs text-white">
                                    @if(!$isVisibleDateDecisionField)
                                    <i class="fa-solid fa-circle-plus fa-lg"></i>
                                    @else
                                    <i class="fa-solid fa-circle-minus fa-lg"></i>
                                    @endif
                                </button>
                            </div>
                            @error('dateDecision') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="number-decision-id" class="font-semibold">Номер решения<span class="ml-1 text-red-500">*</span></label>
                            <div class="flex">
                                @if(!$isVisibleNumberDecisionField)
                                <select wire:model="numberDecision" name="number_decision" id="number-decision-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    <option value="0">Выберите номер решения</option>
                                    @foreach($listOfNumberDecision as $value)
                                    <option value="{{ $value->number_decision }}">{{ $value->number_decision }}</option>
                                    @endforeach
                                </select>
                                @else
                                <input wire:model="numberDecision" type="text" name="number_decision" id="number-decision-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @endif
                                <button wire:click="visibleNumberDecisionField" class="ml-1 bg-blue-500 hover:bg-blue-600 rounded px-2.5 py-1.5 text-xs text-white">
                                    @if(!$isVisibleNumberDecisionField)
                                    <i class="fa-solid fa-circle-plus fa-lg"></i>
                                    @else
                                    <i class="fa-solid fa-circle-minus fa-lg"></i>
                                    @endif
                                </button>
                            </div>
                            @error('numberDecision') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="position-id" class="font-semibold">Должность<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="position" name="position" id="position-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите должность</option>
                            @foreach($positions as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('position') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="task-id" class="font-semibold">Задача<span class="ml-1 text-red-500">*</span></label>
                        <textarea wire:model="task" type="text" name="task" id="task-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('task') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="due-date-id" class="font-semibold">Срок исполнения<span class="ml-1 text-red-500">*</span></label>
                        <div class="flex">
                            @if(!$isVisibleDueDateField)
                            <select wire:model="dueDate" name="dueDate" id="due-date-id" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Выберите срок исполнения</option>
                                @foreach($listOfDueDate as $value)
                                <option value="{{ $value->due_date }}">{{ $value->due_date }}</option>
                                @endforeach
                            </select>
                            @else
                            <input wire:model="dueDate" type="text" name="dueDate" id="due-date-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @endif
                            <button wire:click="visibleDueDateField" class="ml-1 bg-blue-500 hover:bg-blue-600 rounded px-2.5 py-1.5 text-xs text-white">
                                @if(!$isVisibleDueDateField)
                                <i class="fa-solid fa-circle-plus fa-lg"></i>
                                @else
                                <i class="fa-solid fa-circle-minus fa-lg"></i>
                                @endif
                            </button>
                        </div>
                        @error('dueDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isCreatingAndEditingModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="{{ (!$isEdit) ? 'store' : 'update' }}" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Создать') : __('Обновить') }}
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
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isFilterModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog to confirm delete record -->
    <x-dialog-modal wire:model="isConfirmDeleteRecordModalVisible" maxWidth="md">
        <x-slot name="title">
            Подтверждение удаления записи
        </x-slot>

        <x-slot name="content">
            <div class="w-full flex flex-col">
                <h2 class="text-lg">Вы действительно хотите удалить данную запись?</h2>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button type="button" wire:click="$toggle('isConfirmDeleteRecordModalVisible')" class="w-full sm:w-12 bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                Нет
            </button>
            <button type="button" wire:click="destroy" class="ml-2 w-full sm:w-12 bg-red-500 hover:bg-red-600 rounded shadow-xl px-2 py-1 text-white">
                Да
            </button>
        </x-slot>
    </x-dialog-modal>
</div>