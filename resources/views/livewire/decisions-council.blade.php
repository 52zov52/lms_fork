<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-end">
        @can('create-edit-delete-decisions-rectorate-and-council')
        <button wire:click="creatingAndEditingModalFormVisible(false)" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-2 text-xs text-left text-green-400 font-bold">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить запись
        </button>
        @endcan
        <button wire:click="filterModalFormVisible" class="block md:hidden bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-2 text-xs text-left text-blue-400 font-bold">
            <i class="fa-solid fa-filter"></i>
            Фильтры
        </button>
    </div>

    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr>
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-60 px-2 py-2 font-semibold">Должность</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">Поручение</th>
                    <th scope="col" class="w-24 px-2 py-2 font-semibold">Срок исполнения</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Дата и номер решения</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Статус</th>
                    @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Действия</th>
                    @endcan
                </tr>
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold"></th>
                    <th scope="col" class="w-60 px-2 py-2 font-semibold">
                        <select wire:model="filterPosition" name="filterPosition" wire:change="setFilterPosition" id="filter-position-id" class="w-60 bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Все должности</option>
                            @foreach($positions as $name)
                            <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold"></th>
                    <th scope="col" class="w-24 px-2 py-2 font-semibold"></th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold"></th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold"></th>
                    @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
                    <th scope="col" class="w-16 px-2 py-2 font-semibold"></th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($decisionsRectorateAndCouncil as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-60 px-2 py-2">{{ $value->position }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->task }}</td>
                    <td class="w-24 px-2 py-2">{{ $value->due_date }}</td>
                    <td class="w-32 px-2 py-2">{{ $value->date_and_number_decision }}</td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value->status == 1)
                        <span class="text-orange-400 font-bold cursor-pointer">Р</span>
                        @elseif($value->status == 2)
                        <span class="text-sky-400 font-bold cursor-pointer">У</span>
                        @else
                        <span class="text-green-400 font-bold cursor-pointer">В</span>
                        @endif
                    </td>
                    @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
                    <td class="w-16 px-2 py-2">
                        <div class="grid gap-1 grid-cols-2">
                            @can('confirm-decisions-rectorate-and-council')
                            <button onclick="showToast('confirmDecisions', '{{ $value->id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-green-400 font-bold" title="" data-tippy-content="Подтверждение">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button onclick="showToast('undoDecisions', '{{ $value->id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-gray-400 font-bold" title="" data-tippy-content="Отменить подтверждение">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                            @endcan
                            @can('create-edit-delete-decisions-rectorate-and-council')
                            <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }})" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-orange-400 font-bold" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }})" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-red-400 font-bold" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endcan
                        </div>
                    </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Mobile -->
    <div class="lg:hidden flex flex-col space-y-2">
        @foreach($decisionsRectorateAndCouncil as $value)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
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
            <div class="flex flex-col">
                <span class="font-semibold">Статус:</span>
                @if($value->status == 1)
                <span class="text-orange-400 font-semibold">В работе</span>
                @elseif($value->status == 2)
                <span class="text-sky-400 font-semibold">На утверждение</span>
                @else
                <span class="text-green-400 font-semibold">Выполнено</span>
                @endif
            </div>
            @canany(['create-edit-delete-decisions-rectorate-and-council', 'confirm-decisions-rectorate-and-council'])
            <div class="flex flex-col">
                <span class="font-semibold">Действия:</span>
                <div class="flex flex-row space-x-2">
                    @can('confirm-decisions-rectorate-and-council')
                    <button onclick="showToast('confirmDecisions', '{{ $value->id }}')" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-green-400 font-bold" title="" data-tippy-content="Подтверждение">
                        <i class="fa-solid fa-check"></i>
                    </button>
                    <button onclick="showToast('undoDecisions', '{{ $value->id }}')" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-gray-400 font-bold" title="" data-tippy-content="Отменить подтверждение">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                    @endcan
                    @can('create-edit-delete-decisions-rectorate-and-council')
                    <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }})" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-orange-400 font-bold" title="" data-tippy-content="Редактировать">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }})" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-red-400 font-bold" title="" data-tippy-content="Удалить">
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
                <button id="btn-id" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
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
            <button type="button" onclick="showToast('destroy')" class="ml-2 w-full sm:w-12 bg-red-500 hover:bg-red-600 rounded shadow-xl px-2 py-1 text-white">
                Да
            </button>
        </x-slot>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    const element = document.getElementById('btn-id');
    element.addEventListener('click', function(event) {
        event.preventDefault();
        if (!@this.isEdit) {
            showToast('store');
        } else {
            showToast('update');
        }
    });
    async function showToast(methodName, param = null) {
        await @this.callMethod(methodName, param);;

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
    }
</script>
@endpush