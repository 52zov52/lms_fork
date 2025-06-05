<div class="h-full">
    <div class="h-full flex flex-col space-y-2">

        <div class="flex flex-col md:flex-row md:justify-between space-y-2 md:space-y-0">
            <div class="flex flex-row space-x-2">
                <span class="bg-orange-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Федеральные</span>
                <span class="bg-sky-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">Региональные</span>
                <span class="bg-green-400 rounded px-2 py-1 text-xs md:text-sm text-white font-semibold">В рамках Академии</span>
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
                                        <button wire:click="edit({{ $value->id }})" class="w-9 bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white" title="" data-tippy-content="Редактировать">
                                            <span wire:loading.remove wire:target="edit({{ $value->id }})">
                                                <i class="fa-solid fa-pen"></i>
                                            </span>
                                            <span wire:loading wire:target="edit({{ $value->id }})">
                                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                            </span>
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
                                <button wire:click="edit({{ $value->id }})" class="w-auto grow btn-edit" data-tippy-content="Редактировать">
                                    <span wire:loading.remove wire:target="edit({{ $value->id }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </span>
                                    <span wire:loading wire:target="edit({{ $value->id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
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
                <x-alert type="info" title="Информация" message="Список мероприятий по воспитательной работы и социализации отсутствует" />
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
    <x-dialog-modal wire:model="isEditModalVisible" maxWidth="4xl">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                Форма редактирования записи
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="link-to-news" class="font-semibold">Ссылка на пост в соц.сети<span class="ml-1 text-red-500">*</span></label>
                        <textarea wire:model="linkToNews" name="linkToNews" id="link-to-news" cols="5" rows="2" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('linkToNews') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <label for="file-id" class="font-semibold">Файл<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="file" type="file" name="file" id="file-id-{{ $rand }}" class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.zip</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 5 МБ</span>
                        <div wire:loading wire:target="file" class="text-green-500 font-semibold">Идет загрузка файла ...</div>
                        @error('file') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        
                        @if(!is_null($filePath))
                            <div class="mt-2 flex">
                                <a href="storage/{{ $filePath }}" target="_blank" class="text-blue-500 hover:underline" title="Скачать файл">
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
                <button wire:click="$toggle('isEditModalVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click="update" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    <span wire:loading.remove wire:target="update">
                        Обновить
                    </span>
                    <span wire:loading wire:target="update">
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
                    @if(!is_null($filePath))
                    <div class="flex flex-col bg-gray-100 border rounded p-2">
                        <span><b>Прикреплённые файлы:</b></span>
                        <ul>
                            <li>
                                <a href="{{ asset('storage/'. $filePath) }}" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">{{ $filename }}</a>
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