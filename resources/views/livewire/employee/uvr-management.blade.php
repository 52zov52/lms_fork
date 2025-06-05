<div class="h-full">
    <div class="h-full flex flex-col space-y-2">

        <div class="flex flex-col md:flex-row md:justify-between space-y-2 md:space-y-0">
            <div class="flex flex-row space-x-2">
                <span class="bg-orange-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Федеральные</span>
                <span class="bg-sky-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Региональные</span>
                <span class="bg-green-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">В рамках Академии</span>
            </div>

            <div class="flex flex-col space-x-0 space-y-1 md:flex-row md:space-x-2 md:space-y-0">
                <button wire:click="toggleCreateOrEditModalVisible(0)" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-2 text-xs text-center text-green-400 hover:text-white font-bold">
                    <span wire:loading.remove wire:target="toggleCreateOrEditModalVisible(0)">
                        <i class="fa-solid fa-circle-plus"></i>
                        Добавить запись
                    </span>
                    <span wire:loading wire:target="toggleCreateOrEditModalVisible(0)">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
                <button wire:click="downloadReport" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-center text-blue-400 hover:text-white font-bold">
                    <span wire:loading.remove wire:target="downloadReport">
                        <i class="fa-solid fa-download"></i> Скачать отчет
                    </span>
                    <span wire:loading wire:target="downloadReport">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
            </div>
        </div>

        <div wire:loading.remove wire:target="getData">
            @if(!is_null($data) && count($data) != 0)
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Мероприятие</th>
                            <th scope="col" class="w-56 px-2 py-2 text-left font-semibold">Ответственное лицо</th>
                            <th scope="col" class="w-16 px-2 py-2 font-semibold">Дата проведения</th>
                            <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Учебный год и полугодие</th>
                            <th scope="col" class="w-44 px-2 py-2 text-left font-semibold">Ссылка</th>
                            <th scope="col" class="w-16 px-2 py-2 font-semibold">Файл(ы)</th>
                            <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Категория</th>
                            <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $value)
                            <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                                <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                                <td class="w-auto px-2 py-2">
                                    {{ $value->name }}
                                </td>
                                <td class="w-56 px-2 py-2">{{ $value->fio }}</td>
                                <td class="w-16 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($value->date)->format('d.m.Y') }}</td>
                                <td class="w-32 px-2 py-2 text-center">{{ $value->academic_year }} ({{ $value->half_year }})</td>
                                <td class="w-44 px-2 py-2">
                                    @if(!is_null($value->link_to_news))
                                        <a href="{{ $value->link_to_news }}" class="text-blue-400 hover:underline" target="_blank">{{ $value->link_to_news }}</a>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td class="w-16 px-2 py-2 text-center">
                                    @if(!is_null($value->file_path))
                                        <a href="{{ asset('storage/'. $value->file_path) }}" class="text-blue-400 hover:underline" target="_blank">Смотреть</a>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td class="w-24 px-2 py-2 text-center">
                                    @if($value->category == 1)
                                        <span class="text-orange-400 font-bold cursor-pointer">Ф</span>
                                    @elseif($value->category == 2)
                                        <span class="text-sky-400 font-bold cursor-pointer">Р</span>
                                    @elseif($value->category == 3)
                                        <span class="text-green-400 font-bold cursor-pointer">А</span>
                                    @else
                                        <span>-</span>
                                    @endif
                                </td>
                                <td class="w-32 px-2 py-2 text-center">
                                    <div class="flex flex-row items-center justify-center space-x-1">
                                        <button wire:click="showDescription({{ $value->id }})" class="w-9 btn-description" data-tippy-content="Посмотреть">
                                            <span wire:loading.remove wire:target="showDescription({{ $value->id }})">
                                                <i class="fa-solid fa-up-right-from-square"></i>
                                            </span>
                                            <span wire:loading wire:target="showDescription({{ $value->id }})">
                                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                            </span>
                                        </button>
                                        <button wire:click="toggleCreateOrEditModalVisible({{ $value->id }}, true)" class="w-9 bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white" title="" data-tippy-content="Редактировать">
                                            <span wire:loading.remove wire:target="toggleCreateOrEditModalVisible({{ $value->id }}, true)">
                                                <i class="fa-solid fa-pen"></i>
                                            </span>
                                            <span wire:loading wire:target="toggleCreateOrEditModalVisible({{ $value->id }}, true)">
                                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                            </span>
                                        </button>
                                        <button onclick="confirmDestroy('{{ $value->id }}')" class="w-9 bg-white hover:bg-red-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 px-2 py-1 text-xs text-center text-red-400 hover:text-white" title="" data-tippy-content="Удалить">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($data as $value)
                    <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                        <div class="flex flex-col">
                            <span class="font-semibold">Мероприятие:</span>
                            <span>{{ $value->name }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Ответственное лицо:</span>
                            <span>{{ $value->fio }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Дата проведения:</span>
                            <span>{{ \Carbon\Carbon::parse($value->date)->format('d.m.Y') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Учебный год и полугодие:</span>
                            <span>{{ $value->academic_year }} ({{ $value->half_year }})</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Ссылка:</span>
                            @if(!is_null($value->link_to_news))
                                <a href="{{ $value->link_to_news }}" class="text-blue-400 hover:underline" target="_blank">{{ $value->link_to_news }}</a>
                            @else
                                <span>-</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Файл(ы):</span>
                            @if(!is_null($value->file_path))
                                <a href="{{ asset('storage/'. $value->file_path) }}" class="text-blue-400 hover:underline" target="_blank">Смотреть</a>
                            @else
                                <span>-</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Категория:</span>
                            @if($value->category == 1)
                                <span class="text-orange-400 font-semibold">Федеральные</span>
                            @elseif($value->category == 2)
                                <span class="text-sky-400 font-semibold">Региональные</span>
                            @else
                                <span class="text-green-400 font-semibold">В рамках Академии</span>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="font-semibold">Действия:</span>
                            <div class="flex flex-row space-x-2">
                                <button wire:click="showDescription({{ $value->id }})" class="w-auto grow btn-description" data-tippy-content="Посмотреть">
                                    <span wire:loading.remove wire:target="showDescription({{ $value->id }})">
                                        <i class="fa-solid fa-up-right-from-square"></i>
                                    </span>
                                    <span wire:loading wire:target="showDescription({{ $value->id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                <button wire:click="toggleCreateOrEditModalVisible({{ $value->id }}, true)" class="w-auto grow btn-edit" data-tippy-content="Редактировать">
                                    <span wire:loading.remove wire:target="toggleCreateOrEditModalVisible({{ $value->id }}, true)">
                                        <i class="fa-solid fa-pen"></i>
                                    </span>
                                    <span wire:loading wire:target="toggleCreateOrEditModalVisible({{ $value->id }}, true)">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                <button onclick="confirmDestroy('{{ $value->id }}')" class="w-auto grow btn-destroy" title="" data-tippy-content="Удалить">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $data->links() }}
            </div>
            @else
                <x-alert type="info" title="Информация" message="Список мероприятия по воспитательной работы и социализации отсутствует" />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="getData">
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
                        <label for="category" class="font-semibold">Категория мероприятий<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="category" name="category" id="category" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите категорию мероприятия из списка</option>
                            @foreach($listOfCategories as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('category') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="name" class="font-semibold">Наименование мероприятия<span class="ml-1 text-red-500">*</span></label>
                        <textarea wire:model="name" name="name" id="name" cols="5" rows="2" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('name') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description" class="font-semibold">Описание</label>
                        <textarea wire:model="description" name="description" id="description" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col">
                        <label for="date" class="font-semibold">Дата проведения<span class="ml-1 text-red-400">*</span></label>
                        <input wire:model="date" type="date" id="date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('date') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="academic-year" class="font-semibold">Учебный год<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="academicYear" type="text" id="academic-year" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('academicYear') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="half-year" class="font-semibold">Полугодие<span class="ml-1 text-red-400">*</span></label>
                            <select wire:model="halfYear" name="halfYear" id="half-year" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Выберите из списка полугодие</option>
                                <option value="1">Первое</option>
                                <option value="2">Второе</option>
                            </select>
                            @error('halfYear') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="user-id" class="font-semibold">Ответственное лицо<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="userId" name="userId" id="user-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите ответственное лицо</option>
                            @foreach($listOfUsers as $key => $value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('userId') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <label for="file-id" class="font-semibold">Файл<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="file" type="file" name="file" id="file-id-{{ $rand }}" class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.pdf, *.doc, *.docx, *.xls, *.xlsx, *.jpg, *.bmp, *.png, .*zip</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 3 МБ</span>
                        <div wire:loading wire:target="file" class="text-green-500 font-semibold">Идет загрузка файла ...</div>
                        @error('file') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        
                        @if($isEdit && !is_null($file_path))
                            <div class="mt-2 flex">
                                <a href="{{ asset('storage/'. $file_path) }}" target="_blank" class="text-blue-500 hover:underline" title="Скачать файл">
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
                    <div class="flex flex-col">
                        <span><b>Описание:</b></span>
                        <textarea cols="5" rows="10" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" readonly>{{ $description }}</textarea>
                    </div>
                    @if(!is_null($file_path))
                    <div class="flex flex-col bg-gray-100 border rounded p-2">
                        <span><b>Прикреплённые файлы:</b></span>
                        <ul>
                            <li>
                                <a href="{{ asset('storage/'. $file_path) }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">{{ $filename }}</a>
                            </li>
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