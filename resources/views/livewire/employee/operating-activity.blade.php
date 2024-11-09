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
                <label for="selected-week" class="text-xs">
                    Период
                    <span wire:loading wire:target="setMonth">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </label>
                <select wire:model="selectedWeek" wire:change="setWeek" name="selectedWeek" id="selected-week" class="w-full md:w-auto form-select">
                    <option value="0">Выберите неделю из списка</option>
                    @foreach($listOfWeeks as $value)
                    <option value="{{ $value->week }}">{{ $value->week }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col space-y-1">
                <label for="selected-user-id" class="text-xs">Исполнитель</label>
                <select wire:model="selectedUserId" wire:change="setUserId" name="selectedUserId" id="selected-user-id" class="w-full md:w-auto form-select">
                    <option value="0">Выберите исполнителя из списка</option>
                    @foreach($listOfEmployees as $value)
                    <option value="{{ $value->user_id }}">{{ $value->fio }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="flex flex-col md:flex-row md:justify-between space-y-2 md:space-y-0">
            <div class="flex flex-row space-x-2">
                <span class="bg-orange-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">В работе</span>
                <span class="bg-sky-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">На утверждении</span>
                <span class="bg-green-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Выполнена</span>
            </div>
            @if(!auth()->user()->can('rector'))
            <button wire:click="show" class="w-auto md:w-36 btn-add">
                <span wire:loading.remove wire:target="show">
                    <i class="fa-solid fa-circle-plus"></i>
                    Добавить запись
                </span>
                <span wire:loading wire:target="show">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </span>
            </button>
            @endif
        </div>

        <div wire:loading.remove wire:target="setWeek, setUserId">
            @if(!is_null($operatingActivities) && count($operatingActivities) != 0)
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-60 px-2 py-2 text-left font-semibold">Направление деятельности</th>
                            <th scope="col" class="w-36 px-2 py-2 text-center font-semibold">Исполнитель</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Краткое описание выполненной работы</th>
                            <th scope="col" class="w-24 px-2 py-2 text-left font-semibold">Период</th>
                            <th scope="col" class="w-16 px-2 py-2 font-semibold">Статус</th>
                            <th scope="col" class="w-36 px-2 py-2 text-center font-semibold">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($operatingActivities as $operatingActivity)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-60 px-2 py-2">{{ $operatingActivity->activity_direction }}</td>
                            <td class="w-36 px-2 py-2 text-center">{{ $operatingActivity->fio }}</td>
                            <td class="w-auto px-2 py-2 text-left text-justify">{{ Str::limit($operatingActivity->description, 255, ' ...') }}</td>
                            <td class="w-24 px-2 py-2 text-left">{{ $operatingActivity->week }}</td>
                            <td class="w-16 px-2 py-2 text-center">
                                @if($operatingActivity->status == 1)
                                <span class="text-orange-400 font-bold cursor-pointer">Р</span>
                                @elseif($operatingActivity->status == 2)
                                <span class="text-sky-400 font-bold cursor-pointer">У</span>
                                @else
                                <span class="text-green-400 font-bold cursor-pointer">В</span>
                                @endif
                            </td>
                            <td class="w-36 text-center">
                                <div class="flex flex-row flex-wrap items-center justify-center space-x-1">
                                    <button wire:click="showDescription({{ $operatingActivity->id }})" class="w-9 btn-description" data-tippy-content="Посмотреть">
                                        <span wire:loading.remove wire:target="showDescription({{ $operatingActivity->id }})">
                                            <i class="fa-solid fa-up-right-from-square"></i>
                                        </span>
                                        <span wire:loading wire:target="showDescription({{ $operatingActivity->id }})">
                                            <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                        </span>
                                    </button>
                                    @can('confirm-operating-activity')
                                    <button wire:click="confirm({{ $operatingActivity->id }})" class="w-9 btn-check" data-tippy-content="Подтверждение">
                                        <span wire:loading.remove wire:target="confirm({{ $operatingActivity->id }})">
                                            <i class="fa-solid fa-check"></i>
                                        </span>
                                        <span wire:loading wire:target="confirm({{ $operatingActivity->id }})">
                                            <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                        </span>
                                    </button>
                                    <button wire:click="undoConfirm({{ $operatingActivity->id }})" class="w-9 btn-undo" data-tippy-content="Отменить подтверждение">
                                        <span wire:loading.remove wire:target="undoConfirm({{ $operatingActivity->id }})">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </span>
                                        <span wire:loading wire:target="undoConfirm({{ $operatingActivity->id }})">
                                            <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                        </span>
                                    </button>
                                    @endcan
                                    @if($operatingActivity->user_id == auth()->user()->id)
                                        @if($operatingActivity->status != 3)
                                        <button wire:click="edit({{ $operatingActivity->id }})" class="w-9 btn-edit" data-tippy-content="Редактировать">
                                            <span wire:loading.remove wire:target="edit({{ $operatingActivity->id }})">
                                                <i class="fa-solid fa-pen"></i>
                                            </span>
                                            <span wire:loading wire:target="edit({{ $operatingActivity->id }})">
                                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                            </span>
                                        </button>
                                        <button onclick="confirmDestroy('{{ $operatingActivity->id }}')" class="w-9 btn-destroy" title="" data-tippy-content="Удалить">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($operatingActivities as $operatingActivity)
                <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                    <div class="flex flex-col">
                        <span class="font-semibold">Направление деятельности:</span>
                        <span>{{ $operatingActivity->activity_direction }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Исполнитель:</span>
                        <span>{{ $operatingActivity->fio }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Краткое описание выполненной работы:</span>
                        <span class="text-justify">{{ Str::limit($operatingActivity->description, 255, ' ...') }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Период:</span>
                        <span>{{ $operatingActivity->week }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Статус:</span>
                        @if($operatingActivity->status == 1)
                        <span class="text-orange-400 font-semibold">В работе</span>
                        @elseif($operatingActivity->status == 2)
                        <span class="text-sky-400 font-semibold">На утверждении</span>
                        @else
                        <span class="text-green-400 font-semibold">Выполнена</span>
                        @endif
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Действия:</span>
                        <div class="flex flex-row space-x-2">
                            <button wire:click="showDescription({{ $operatingActivity->id }})" class="w-auto grow btn-description" data-tippy-content="Посмотреть">
                                <span wire:loading.remove wire:target="showDescription({{ $operatingActivity->id }})">
                                    <i class="fa-solid fa-up-right-from-square"></i>
                                </span>
                                <span wire:loading wire:target="showDescription({{ $operatingActivity->id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            @can('confirm-operating-activity')
                            <button wire:click="confirm({{ $operatingActivity->id }})" class="w-auto grow btn-check" data-tippy-content="Подтверждение">
                                <span wire:loading.remove wire:target="confirm({{ $operatingActivity->id }})">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span wire:loading wire:target="confirm({{ $operatingActivity->id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            <button wire:click="undoConfirm({{ $operatingActivity->id }})" class="w-auto grow btn-undo" data-tippy-content="Отменить подтверждение">
                                <span wire:loading.remove wire:target="undoConfirm({{ $operatingActivity->id }})">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </span>
                                <span wire:loading wire:target="undoConfirm({{ $operatingActivity->id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            @endcan
                            @if($operatingActivity->user_id == auth()->user()->id)
                                @if($operatingActivity->status != 3)
                                <button wire:click="edit({{ $operatingActivity->id }})" class="w-auto grow btn-edit" data-tippy-content="Редактировать">
                                    <span wire:loading.remove wire:target="edit({{ $operatingActivity->id }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </span>
                                    <span wire:loading wire:target="edit({{ $operatingActivity->id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                <button onclick="confirmDestroy('{{ $operatingActivity->id }}')" class="w-auto grow btn-destroy" title="" data-tippy-content="Удалить">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $operatingActivities->links() }}
            </div>
            @else
            <x-alert type="info" title="Информация" message="Отчеты по операционной деятельности Университета отсутствуют за выбранный вами период времени" />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="setWeek, setUserId">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных</span>
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
                    <div class="flex flex-col">
                        <span class="font-semibold">Период отчета<span class="text-red-500">*</span></span>
                        <div class="flex flex-row space-x-2 py-1">
                            <div class="w-full flex flex-col">
                                <label for="week-start-date" class="font-semibold">с</label>
                                <input wire:model="weekStartDate" type="date" id="week-start-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @error('weekStartDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-full flex flex-col">
                                <label for="week-end-date" class="font-semibold">по</label>
                                <input wire:model="weekEndDate" type="date" id="week-end-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @error('weekEndDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="activity-direction" class="font-semibold">Направления деятельности<span class="text-red-500">*</span></label>
                        <select wire:model="activityDirection" name="activityDirection" id="activity-direction" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите направление деятельности</option>
                            @foreach($listOfActivityDirections as $value)
                            <option value="{{ $value->name }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                        @error('activityDirection') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description" class="font-semibold">Описание выполненной работы<span class="text-red-500">*</span></label>
                        <textarea wire:model="description" name="description" id="description" cols="5" rows="10" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="status" class="font-semibold">Статус<span class="text-red-500">*</span></label>
                        <select wire:model="status" name="status" id="status" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите статус</option>
                            <option value="1">В работе</option>
                            <option value="2">На утверждение</option>
                        </select>
                        @error('status') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <label for="file" class="font-semibold">Файл</label>
                            <input wire:model="files" type="file" name="file" id="file-{{ $rand }}" multiple class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.pdf, *.doc, *.docx, *.xls, *.xlsx, *.jpg, *.bmp, *.png</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 3 МБ</span>
                        <div wire:loading wire:target="files" class="text-green-500 font-semibold">Идет загрузка файла ...</div>
                        @error('files') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        @if(!$isEdit)
                            @if(count($files) != 0)
                            <div class="mt-2 flex flex-col bg-gray-100 border rounded p-2">
                                <span><b>Прикреплённые файлы:</b></span>
                                <ul>
                                    @foreach ($files as $key => $value)
                                    <li>
                                        {{ $value->getClientOriginalName() }}
                                        <button type="button" wire:click="removeFileFromArray({{ $key }})" class="ml-2 text-red-500 underline">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        @else
                        @if(count($storedFiles) != 0 || count($files) != 0)
                        <div class="mt-2 flex flex-col bg-gray-100 border rounded p-2">
                            <span><b>Прикреплённые файлы:</b></span>
                            <ul>
                                @foreach ($storedFiles as $key => $value)
                                <li>
                                    <a href="{{ asset('storage/'. $value->file_path) }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">{{ $value->filename }}</a>
                                    <button type="button" wire:click="removeFileFromStorage({{ $value->id }})" class="ml-2 text-red-500 underline">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </li>
                                @endforeach
                                @if(count($files) != 0)
                                    @foreach ($files as $key => $value)
                                    <li>
                                        {{ $value->getClientOriginalName() }}
                                        <button type="button" wire:click="removeFileFromArray({{ $key }})" class="ml-2 text-red-500 underline">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        @endif
                        @endif
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

    <!-- Modal dialog to show description record -->
    <x-dialog-modal wire:model="isDescriptionModalVisible" maxWidth="4xl">
        <form>
            @csrf
            <x-slot name="title">
                Форма просмотра записи
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <span><b>Испольнитель:</b> {{ $executor }}</span>
                    <span><b>Период:</b> {{ $week }}</span>
                    <span><b>Направления деятельности:</b> {{ $activityDirection }}</span>
                    <div class="flex flex-col">
                        <span><b>Описание выполненной работы:</b></span>
                        <textarea cols="5" rows="10" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" readonly>{{ $description }}</textarea>
                    </div>
                    @if(count($storedFiles) != 0)
                    <div class="flex flex-col bg-gray-100 border rounded p-2">
                        <span><b>Прикреплённые файлы:</b></span>
                        <ul>
                            @foreach ($storedFiles as $file)
                            <li>
                                <a href="{{ asset('storage/'. $file->file_path) }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">{{ $file->filename }}</a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isDescriptionModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
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
</script>
@endpush