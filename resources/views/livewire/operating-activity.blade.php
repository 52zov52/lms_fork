<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-between">
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <select wire:model="filterYear" wire:change="setFilterYear" name="filter-year" id="filter-year-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите год</option>
                @foreach($years as $year)
                <option value="{{ $year->year }}">{{ $year->year }}</option>
                @endforeach
            </select>
            <select wire:model="filterMonthYear" wire:change="setFilterMonthYear" name="filter-month-year" id="filter-month-year-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите месяц</option>
                @foreach($monthsYears as $monthYear)
                <option value="{{ $monthYear->monthYear }}">{{ $monthYear->monthYear }}</option>
                @endforeach
            </select>
            <select wire:model="filterWeeks" wire:change="setFilterWeeks" name="filter-weeks" id="filter-week-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="0">Выберите неделю</option>
                @foreach($weeksRange as $weekRange)
                <option value="{{ $weekRange }}">{{ $weekRange }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            <button wire:click="creatingAndEditingModalFormVisible(false)" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-left text-blue-400 font-bold">
                <i class="fa-solid fa-circle-plus fa-lg"></i>
                Добавить запись
            </button>
            <button wire:click="generateOperatingActivityReport" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-left text-green-400 font-bold">
                <i class="fa-solid fa-file-excel"></i>
                Скачать отчет
            </button>
            <button wire:click="filterModalFormVisible" class="block md:hidden bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-left text-gray-400 dark:text-white font-bold">
                <i class="fa-solid fa-filter"></i>
                Фильтры
            </button>
        </div>
    </div>

    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr>
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-60 px-2 py-2 font-semibold">Направление деятельности</th>
                    <th scope="col" class="w-40 px-2 py-2 font-semibold">Исполнитель</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">Задачи</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Статус</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Действия</th>
                </tr>
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2"></th>
                    <th scope="col" class="w-60 px-2 py-2 font-semibold">
                        <select wire:model="filterActivityDirection" wire:change="setFilterActivityDirection" name="filterActivityDirection" id="filter-activity-direction-id" class="w-60 bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Все направления деятельности</option>
                            @foreach($listOfActivityDirections as $value)
                            <option value="{{ $value->name }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </th>
                    <th scope="col" class="w-40 px-2 py-2 font-semibold">
                        <select wire:model="filterExecutor" wire:change="setFilterExecutor" name="filterExecutor" id="filter-executor-id" class="w-40 bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Все исполнители</option>
                            @foreach($listOfExecutors as $value)
                            <option value="{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}">{{ $value->last_name }} {{ mb_substr($value->first_name, 0, 1) }}. {{ mb_substr($value->middle_name, 0, 1) }}.</option>
                            @endforeach
                        </select>
                    </th>
                    <th scope="col" class="w-auto px-2 py-2"></th>
                    <th scope="col" class="w-16 px-2 py-2"></th>
                    <th scope="col" class="w-16 px-2 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($operatingActivity as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-60 px-2 py-2 ">{{ $value->activity_direction }}</td>
                    <td class="w-40 px-2 py-2">{{ $value->fio }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->task }}</td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value->status == 1)
                        <span class="text-orange-400 font-bold cursor-pointer">Р</span>
                        @elseif($value->status == 2)
                        <span class="text-sky-400 font-bold cursor-pointer">У</span>
                        @else
                        <span class="text-green-400 font-bold cursor-pointer">В</span>
                        @endif
                    </td>
                    <td class="w-16 px-2 py-2">
                        <div class="grid gap-1 grid-cols-2">
                            @can('confirm-operating-activity')
                            <button onclick="showToast('confirmActivity', '{{ $value->id }}')" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-green-400 font-bold" title="" data-tippy-content="Подтверждение">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            @endcan
                            <button wire:click="descriptionModalFormVisible({{ $value->id }})" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-indigo-400 font-bold" title="" data-tippy-content="Доп. информация">
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </button>
                            <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }}, {{ $value->user_id }})" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-sky-400 font-bold" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }}, {{ $value->user_id }})" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-red-400 font-bold" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Mobile -->
    <div class="lg:hidden flex flex-col space-y-2">
        @foreach($operatingActivity as $value)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
            <div class="flex flex-col">
                <span class="font-semibold">Направление деятельности:</span>
                <span>{{ $value->activity_direction }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Исполнитель:</span>
                <span>{{ $value->fio }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Задача:</span>
                <span>{{ $value->task }}</span>
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
            <div class="flex flex-col">
                <span class="font-semibold">Действия:</span>
                <div class="flex flex-row space-x-2">
                    @can('confirm-operating-activity')
                    <button onclick="showToast('confirmActivity', '{{ $value->id }}')" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-green-400 font-bold" title="" data-tippy-content="Подтверждение">
                        <i class="fa-solid fa-check"></i>
                    </button>
                    @endcan
                    <button wire:click="descriptionModalFormVisible({{ $value->id }})" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-indigo-400 font-bold" title="" data-tippy-content="Доп. информация">
                        <i class="fa-solid fa-up-right-from-square"></i>
                    </button>
                    @if($value->status != 3)
                    <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }}, {{ $value->user_id }})" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-sky-400 font-bold" title="" data-tippy-content="Редактировать">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }}, {{ $value->user_id }})" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-red-400 font-bold" title="" data-tippy-content="Удалить">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-2">
        {{ $operatingActivity->links() }}
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
                        <span class="font-semibold">Период</span>
                        <div class="flex flex-row space-x-2 py-1">
                            <div class="w-full flex flex-col">
                                <label for="start-date-id" class="font-semibold">с</label>
                                <input wire:model="startDate" type="text" id="start-date-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" tabindex="-1" disabled>
                                @error('startDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                            </div>
                            <div class="w-full flex flex-col">
                                <label for="end-date-id" class="font-semibold">по</label>
                                <input wire:model="endDate" type="text" id="end-date-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" tabindex="-1" disabled>
                                @error('endDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="activity-direction-id" class="font-semibold">Направление деятельности<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="activityDirection" name="activity_direction" id="activity-direction-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите направление деятельности</option>
                            @foreach($listOfActivityDirectionsForUser as $value)
                            <option value="{{ $value->name }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                        @error('activityDirection') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="task-id" class="font-semibold">Задача<span class="ml-1 text-red-500">*</span></label>
                        <textarea wire:model="task" name="task" id="task-id" cols="5" rows="2" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('task') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description-id" class="font-semibold">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col">
                        <label for="status-id" class="font-semibold">Статус<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="status" name="status" id="status-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите статус</option>
                            <option value="1">В работе</option>
                            <option value="2">На утверждение</option>
                        </select>
                        @error('status') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <label for="file-id" class="font-semibold">Файл<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="files" type="file" name="file" id="file-id-{{ $rand }}" multiple class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.pdf, *.doc, *.docx, *.xls, *.xlsx, *.jpg, *.bmp, *.png</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 3 МБ</span>
                        <div wire:loading wire:target="files" class="text-green-500 font-semibold">Идет загрузка файла ...</div>
                        @error('files') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        @if($isEdit)
                        @if(count($userFiles) != 0)
                        <span class="font-semibold">Файл(ы): </span>
                        <ul>
                            @foreach ($userFiles as $userFile)
                            <li>
                                {{ $userFile->filename }}
                                <button type="button" wire:click="removeFile({{ $userFile->id }})" class="ml-2 text-red-500 underline">Удалить</button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @else
                        @if(count($files) != 0)
                        <span class="font-semibold">Файл(ы): </span>
                        <ul>
                            @foreach ($files as $index => $file)
                            <li>
                                {{ $file->getClientOriginalName() }}
                                <button type="button" wire:click="removeFileFromArray({{ $index }})" class="ml-2 text-red-500 underline">Удалить</button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @endif
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
                        <label for="description-id" class="font-semibold">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" readonly></textarea>
                    </div>
                    <div>
                        @if(!is_null($userFiles) && count($userFiles) != 0)
                        <span class="font-semibold">Файл(ы): </span>
                        <ul>
                            @foreach ($userFiles as $userFile)
                            <li>
                                {{ $userFile->filename }}
                                <a href="storage/{{ $userFile->file_path }}" target="_blank" rel="noopener noreferrer" class="ml-2 text-blue-500 hover:underline">cкачать</a>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isDescriptionModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
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
                        <select wire:model="filterActivityDirection" wire:change="setFilterActivityDirection" name="filterActivityDirection" id="filter-activity-direction-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Все направления деятельности</option>
                            @foreach($listOfActivityDirections as $value)
                            <option value="{{ $value->name }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <select wire:model="filterExecutor" wire:change="setFilterExecutor" name="filterExecutor" id="filter-executor-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Все исполнители</option>
                            @foreach($listOfExecutors as $value)
                            <option value="{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}">{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}</option>
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