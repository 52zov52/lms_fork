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

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="filterWeeks" wire:change="setFilterWeeks" name="filter-weeks" id="filter-week-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите неделю</option>
            @foreach($weeksRange as $weekRange)
            <option value="{{ $weekRange }}">{{ $weekRange }}</option>
            @endforeach
        </select>
        <button wire:click="creatingAndEditingModalFormVisible(false)" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить запись
        </button>
        <button wire:click="generateOperatingActivityReport" class="w-full md:w-auto bg-green-500 hover:bg-green-600 rounded px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-file-excel"></i>
            Скачать отчет
        </button>
        <button wire:click="filterModalFormVisible" class="block xl:hidden w-full md:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-2 text-sm text-left text-white">
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
                        <td class="w-72 border px-2 py-2 font-semibold">Направление деятельности</td>
                        <td class="w-60 border px-2 py-2 font-semibold">Исполнитель</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Задачи</td>
                        <td class="w-28 border px-2 py-2 text-center font-semibold">Статус</td>
                        <td class="w-32 border px-2 py-2 text-center font-semibold">Действия</td>
                    </tr>
                    <tr class="bg-gray-100">
                        <td class="w-12 border px-2"></td>
                        <td class="w-72 border px-2">
                            <select wire:model="filterActivityDirection" wire:change="setFilterActivityDirection" name="filterActivityDirection" id="filter-activity-direction-id" class="w-72 border border-gray-300 rounded text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">Все направления деятельности</option>
                                @foreach($listOfActivityDirections as $value)
                                <option value="{{ $value->name }}">{{ $value->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-60 border px-2 py-1">
                            <select wire:model="filterExecutor" wire:change="setFilterExecutor" name="filterExecutor" id="filter-executor-id" class="w-60 border border-gray-300 rounded text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">Все исполнители</option>
                                @foreach($listOfExecutors as $value)
                                <option value="{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}">{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="w-auto border px-2"></td>
                        <td class="w-28 border px-2">
                            <select wire:model="filterStatus" wire:change="setFilterStatus" name="filterStatus" id="filter-status-id" class="w-28 border border-gray-300 rounded text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Все статусы</option>
                                <option value="1">В работе</option>
                                <option value="2">На утверждение</option>
                                <option value="3">Выполнено</option>
                            </select>
                        <td class="w-32 border px-2 text-center"></td>
                    </tr>
                    @foreach($operatingActivity as $value)
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-72 border px-2 py-2">{{ $value->activity_direction }}</td>
                        <td class="w-60 border px-2 py-2">{{ $value->fio }}</td>
                        <td class="w-auto border px-2 py-2">{{ $value->task }}</td>
                        <td class="w-28 border px-2 py-2 text-center">
                            @if($value->status == 1)
                            <div class="flex flex-col bg-orange-500 rounded py-1 text-xs text-white font-bold">
                                <span>В работе</span>
                            </div>
                            @elseif($value->status == 2)
                            <div class="flex flex-col bg-blue-500 rounded py-1 text-xs text-white font-bold">
                                <span>На утверждение</span>
                            </div>
                            @else
                            <div class="flex flex-col bg-green-500 rounded py-1 text-xs text-white font-bold">
                                <span>Выполнено</span>
                            </div>
                            @endif
                        </td>
                        <td class="w-32 border px-2 py-2 text-center">
                            @can('confirm-operating-activity')
                            <button wire:click="confirmActivity({{ $value->id }})" class="bg-green-500 hover:bg-green-600 rounded px-1.5 py-1.5 text-xs text-white" title="Подтверждение">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            @endcan
                            <button wire:click="descriptionModalFormVisible({{ $value->id }})" class="bg-indigo-500 hover:bg-indigo-600 rounded px-1.5 py-1.5 text-xs text-white" title="Доп. информация">
                                <i class="fa-solid fa-up-right-from-square"></i>
                            </button>
                            @if($value->status != 3)
                            <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }}, {{ $value->user_id }})" class="bg-sky-500 hover:bg-sky-600 rounded px-1.5 py-1.5 text-xs text-white" title="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }}, {{ $value->user_id }})" class="bg-red-500 hover:bg-red-600 rounded px-1.5 py-1.5 text-xs text-white" title="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="block xl:hidden flex flex-col space-y-2">
            @foreach($operatingActivity as $value)
            <div class="flex flex-col space-y-2 border shadow-md p-4 text-xs">
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
                    <span class="w-[4.7rem] bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В работе</span>
                    @elseif($value->status == 2)
                    <span class="w-28 bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">На утверждение</span>
                    @else
                    <span class="w-[5.4rem] bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Выполнено</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="flex flex-row space-x-2">
                        @can('confirm-operating-activity')
                        <button wire:click="confirmActivity({{ $value->id }})" title="Подтверждение" class="w-auto grow bg-green-500 hover:bg-green-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        @endcan
                        <button wire:click="descriptionModalFormVisible({{ $value->id }})" title="Доп. информация" class="w-auto grow bg-indigo-500 hover:bg-indigo-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-up-right-from-square"></i>
                        </button>
                        @if($value->status != 3)
                        <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }}, {{ $value->user_id }})" title="Редактировать" class="w-auto grow bg-sky-500 hover:bg-sky-600 rounded px-2 py-1 text-white">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button wire:click="confirmDeleteRecordModalVisible({{ $value->id }}, {{ $value->user_id }})" title="Удалить" class="w-auto grow bg-red-500 hover:bg-red-600 rounded px-2 py-1 text-white">
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
                            <input wire:model="file" type="file" name="file" id="file-id-{{ $rand }}" class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.pdf, *.doc, *.docx, *.jpg, *.bmp, *.png</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 3 МБ</span>
                        <div wire:loading wire:target="file" class="text-green-500 font-semibold">Идет загрузка файла ...</div>
                        @error('file') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        @if($isEdit && !is_null($file_path))
                        <div class="mt-2 flex">
                            <a href="storage/{{ $file_path }}" target="_blank" class="text-blue-500 hover:underline" title="Скачать файл">
                                <i class="fa-solid fa-paperclip"></i> {{ $filename }}
                            </a>
                        </div>
                        <span class="mt-2 w-auto bg-orange-500 rounded p-2 text-white">
                            <i class="fa-solid fa-circle-exclamation"></i> Чтобы заменить файл, загрузите в поле <b>Файл</b> новый файл
                        </span>
                        @endif
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
                    @if(!is_null($file))
                    <div class="flex space-x-2">
                        <span class="font-semibold">Файл: </span>
                        <a href="storage/{{ $file }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">cкачать</a>
                    </div>
                    @endif
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
            <button type="button" wire:click="destroy" class="ml-2 w-full sm:w-12 bg-red-500 hover:bg-red-600 rounded shadow-xl px-2 py-1 text-white">
                Да
            </button>
        </x-slot>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    var pickerStartDate = new Pikaday({
        field: document.getElementById('start-date-id'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            var data = this.getMoment().format('DD.MM.YYYY');
            @this.set('startDate', data);
        }
    });

    var pickerEndDate = new Pikaday({
        field: document.getElementById('end-date-id'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            var data = this.getMoment().format('DD.MM.YYYY');
            @this.set('endDate', data);
        }
    });
</script>
@endpush