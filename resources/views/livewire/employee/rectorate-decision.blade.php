<div class="h-full">
    <div class="h-full flex flex-col space-y-2">

        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
            <div class="flex flex-col space-y-1">
                <label for="selected-year" class="text-xs">Год</label>
                <select wire:model="selectedYear" wire:change="setYear" name="selectedYear" id="selected-year" class="w-full md:w-auto form-select">
                    <option value="0">Выберите год из списка</option>
                    @foreach($listOfYears as $value)
                    <option value="{{ $value->year }}">{{ $value->year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col space-y-1">
                <label for="selected-month" class="text-xs">
                    Месяц
                    <span wire:loading wire:target="setYear">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </label>
                <select wire:model="selectedMonth" wire:change="setMonth" name="selectedMonth" id="selected-month" class="w-full md:w-auto form-select">
                    <option value="0">Выберите месяц из списка</option>
                    @foreach($listOfMonths as $value)
                    <option value="{{ $value->month }}">{{ $value->month }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col space-y-1">
                <label for="selected-due-date" class="text-xs">
                    Срок исполнения
                    <span wire:loading wire:target="setMonth">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </label>
                <select wire:model="selectedDueDate" wire:change="setDueDate" name="selectedDueDate" id="selected-due-date" class="w-full md:w-auto form-select">
                    <option value="0">Выберите срок исполнения из списка</option>
                    @foreach($listOfDueDates as $value)
                    <option value="{{ $value->due_date }}">{{ $value->due_date }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col space-y-1">
                <label for="selected-position" class="text-xs">Должность исполнителя</label>
                <select wire:model="selectedPosition" wire:change="setPosition" name="selectedPosition" id="selected-position" class="w-full md:w-auto form-select">
                    <option value="0">Выберите должность исполнителя из списка</option>
                    @foreach($listOfPositions as $position)
                    <option value="{{ $position }}">{{ $position }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:justify-between space-y-2 md:space-y-0">
            <div class="flex flex-row space-x-2">
                <span class="bg-orange-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">В работе</span>
                <span class="bg-sky-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">На утверждении</span>
                <span class="bg-green-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Выполнено</span>
            </div>
            <div class="flex flex-row space-x-2">
                @can('create-edit-delete-decisions-rectorate-and-council')
                <button wire:click="show" class="w-auto md:w-36 btn-add">
                    <span wire:loading.remove wire:target="show">
                        <i class="fa-solid fa-circle-plus"></i>
                        Добавить запись
                    </span>
                    <span wire:loading wire:target="show">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
                @endcan
                <button wire:click="downloadReport" class="w-auto md:w-36 btn-download">
                    <span wire:loading.remove wire:target="downloadReport">
                        <i class="fa-solid fa-download"></i>
                        Скачать отчет
                    </span>
                    <span wire:loading wire:target="downloadReport">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
            </div>
        </div>

        <div wire:loading.remove wire:target="setDueDate, setPosition">
            @if(!is_null($rectorateDecisions) && count($rectorateDecisions) != 0)
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-72 px-2 py-2 text-left font-semibold">Должность исполнителя</th>
                            <th scope="col" class="w-auto px-2 py-2 text-center font-semibold">Поручение</th>
                            <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Дата и номер решения</th>
                            <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Срок исполнения (до)</th>
                            <th scope="col" class="w-16 px-2 py-2 font-semibold">Статус</th>
                            <th scope="col" class="w-36 px-2 py-2 text-center font-semibold">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rectorateDecisions as $decision)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-72 px-2 py-2 text-left">{{ $decision->position }}</td>
                            <td class="w-auto px-2 py-2 text-left">{{ $decision->task }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $decision->date_and_number_decision }}</td>
                            <td class="w-24 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($decision->due_date)->format('d.m.Y') }}</td>
                            <td class="w-16 px-2 py-2 text-center">
                                @if($decision->status == 1)
                                <span class="text-orange-400 font-bold cursor-pointer">Р</span>
                                @elseif($decision->status == 2)
                                <span class="text-sky-400 font-bold cursor-pointer">У</span>
                                @else
                                <span class="text-green-400 font-bold cursor-pointer">В</span>
                                @endif
                            </td>
                            <td class="w-36 text-center">
                                @if($decision->executor_id == auth()->user()->id)
                                <button onclick="submitReport('{{ $decision->id }}')" class="w-9 btn-description" data-tippy-content="Предоставить отчет">
                                    <i class="fa-solid fa-up-right-from-square"></i>
                                </button>
                                @endif
                                @can('create-edit-delete-decisions-rectorate-and-council')
                                <button wire:click="edit({{ $decision->id }})" class="w-9 btn-edit" data-tippy-content="Редактировать">
                                    <span wire:loading.remove wire:target="edit({{ $decision->id }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </span>
                                    <span wire:loading wire:target="edit({{ $decision->id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                <button onclick="confirmDestroy('{{ $decision->id }}')" class="w-9 btn-destroy" title="" data-tippy-content="Удалить">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @endcan
                                @can('confirm-decisions-rectorate-and-council')
                                <button wire:click="confirm({{ $decision->id }})" class="w-9 btn-check" data-tippy-content="Подтверждение">
                                    <span wire:loading.remove wire:target="confirm({{ $decision->id }})">
                                        <i class="fa-solid fa-check"></i>
                                    </span>
                                    <span wire:loading wire:target="confirm({{ $decision->id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                <button wire:click="undoConfirm({{ $decision->id }})" class="w-9 btn-undo" data-tippy-content="Отменить подтверждение">
                                    <span wire:loading.remove wire:target="undoConfirm({{ $decision->id }})">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </span>
                                    <span wire:loading wire:target="undoConfirm({{ $decision->id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($rectorateDecisions as $decision)
                <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                    <div class="flex flex-col">
                        <span class="font-semibold">Должность исполнителя:</span>
                        <span>{{ $decision->position }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Поручение:</span>
                        <span>{{ $decision->task }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Дата и номер решения:</span>
                        <span>{{ $decision->date_and_number_decision }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Срок исполнения (до):</span>
                        <span>{{ \Carbon\Carbon::parse($decision->due_date)->format('d.m.Y') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Статус:</span>
                        @if($decision->status == 1)
                        <span class="text-orange-400 font-semibold">В работе</span>
                        @elseif($decision->status == 2)
                        <span class="text-sky-400 font-semibold">На утверждении</span>
                        @else
                        <span class="text-green-400 font-semibold">Выполнена</span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Действия:</span>
                        <div class="flex flex-row space-x-2">
                            @if($decision->executor_id == auth()->user()->id)
                            <button onclick="submitReport('{{ $decision->id }}')" class="w-auto grow btn-description" title="" data-tippy-content="Предоставить отчет">
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </button>
                            @endif
                            @can('create-edit-delete-decisions-rectorate-and-council')
                            <button wire:click="edit({{ $decision->id }})" class="w-auto grow btn-edit" data-tippy-content="Редактировать">
                                <span wire:loading.remove wire:target="edit({{ $decision->id }})">
                                    <i class="fa-solid fa-pen"></i>
                                </span>
                                <span wire:loading wire:target="edit({{ $decision->id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            <button onclick="confirmDestroy('{{ $decision->id }}')" class="w-auto grow btn-destroy" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endcan
                            @can('confirm-decisions-rectorate-and-council')
                            <button wire:click="confirm({{ $decision->id }})" class="w-auto grow btn-check" data-tippy-content="Подтверждение">
                                <span wire:loading.remove wire:target="confirm({{ $decision->id }})">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span wire:loading wire:target="confirm({{ $decision->id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            <button wire:click="undoConfirm({{ $decision->id }})" class="w-auto grow btn-undo" data-tippy-content="Отменить подтверждение">
                                <span wire:loading.remove wire:target="undoConfirm({{ $decision->id }})">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </span>
                                <span wire:loading wire:target="undoConfirm({{ $decision->id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $rectorateDecisions->links() }}
            </div>
            @else
            <x-alert type="info" title="Информация" message="Решения ретората Университета отсутствуют." />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="setDueDate, setPosition">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных.</span>
            </div>
        </div>
    </div>

    <!-- Modal dialog to create or edit record -->
    <x-dialog-modal wire:model="isCreateOrEditModalVisible" maxWidth="4xl">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                Форма {{ ($isEdit) ? 'редактирования' : 'добавления' }} записи
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-row space-x-2 py-1">
                        <div class="w-full flex flex-col">
                            <label for="date-decision" class="font-semibold">Дата решения<span class="text-red-500">*</span></label>
                            <input wire:model="dateDecision" type="date" id="date-decision" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('dateDecision') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="w-full flex flex-col">
                            <label for="number-decision" class="font-semibold">Номер решения<span class="text-red-500">*</span></label>
                            <input wire:model="numberDecision" type="text" id="number-decision" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('numberDecision') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="position" class="font-semibold">Должность исполнителя<span class="text-red-500">*</span></label>
                        <select wire:model="position" name="position" id="position" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите должность исполнителя из списка</option>
                            @foreach($listOfPositions as $key => $value)
                            <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('position') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="task" class="font-semibold">Описание поручения для исполнителя<span class="text-red-500">*</span></label>
                        <textarea wire:model="task" name="task" id="task" cols="5" rows="10" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('task') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="due-date" class="font-semibold">Срок исполнения (до)<span class="text-red-500">*</span></label>
                        <input wire:model="dueDate" type="date" id="due-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('dueDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <span class="text-red-500">*Поля обязательные для заполнения</span>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isCreateOrEditModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click="{{ ($isEdit) ? 'update' : 'store' }}" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    <span wire:loading.remove wire:target="store, update">
                        {{ ($isEdit) ? 'Обновить' : 'Добавить' }}
                    </span>
                    <span wire:loading wire:target="store, update">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    function confirmDestroy(id) {
        if (confirm('Вы уверены, что хотите удалить данную запись?')) {
            Livewire.dispatch('destroy', {
                id: id
            })
        }
    }

    function submitReport(id) {
        if (confirm('Вы уверены, что хотите предоставить отчет?')) {
            Livewire.dispatch('submitReport', {
                id: id
            })
        }
    }
</script>
@endpush