<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        @can('create-edit-regulatory-document')
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-end">
            <button wire:click="create" class="w-auto md:w-36 btn-add">
                <span wire:loading.remove wire:target="create">
                    <i class="fa-solid fa-circle-plus"></i>
                    Добавить запись
                </span>
                <span wire:loading wire:target="create">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </span>
            </button>
        </div>
        @endcan

        <div class="flex flex-row space-x-2">
            <input wire:model="searchText" type="text" name="searchText" id="ticket-number" class="w-full bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 px-2 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-gray-600 dark:placeholder-white" placeholder="Для поиска документов введите часть названия документа или его номер (если имеется)">
            <button wire:click="search" class="w-12 bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-1 text-xs text-center text-blue-400 hover:text-white">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <div wire:loading.remove wire:target="fecthData, search">
            @if(!is_null($regulatoryDocuments) && count($regulatoryDocuments) != 0)
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-60 px-2 py-2 text-left font-semibold">Документ</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Описание</th>
                            <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Тип</th>
                            <th scope="col" class="w-40 px-2 py-2 text-center font-semibold">Категория</th>
                            <th scope="col" class="w-36 px-2 py-2 text-center font-semibold">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($regulatoryDocuments as $item)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-60 px-2 py-2">{{ $item->title }}</td>
                            <td class="w-auto px-2 py-2 text-justify">{{ Str::limit($item->description, 255, ' ...') }}</td>
                            <td class="w-24 px-2 py-2 text-center">{{ $item->type }}</td>
                            <td class="w-40 px-2 py-2 text-center">
                                <div class="flex flex-col space-y-1">
                                    @foreach(json_decode($item->category, true) as $value)
                                    <span>{{ $value }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="w-36 text-center">
                                <div class="flex flex-row flex-wrap items-center justify-center space-x-1">
                                    <a href="{{ asset('storage/'. $item->file_path) }}" target="_blank" rel="noopener noreferrer" class="w-9 btn-download" data-tippy-content="Скачать файл">
                                        <i class="fa-solid fa-paperclip"></i>
                                    </a>
                                    @can('create-edit-regulatory-document')
                                    <button wire:click="edit({{ $item->id }})" class="w-9 btn-edit" data-tippy-content="Редактировать">
                                        <span wire:loading.remove wire:target="edit({{ $item->id }})">
                                            <i class="fa-solid fa-pen"></i>
                                        </span>
                                        <span wire:loading wire:target="edit({{ $item->id }})">
                                            <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                        </span>
                                    </button>
                                    <button onclick="confirmDestroy('{{ $item->id }}')" class="w-9 btn-destroy" title="" data-tippy-content="Удалить">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($regulatoryDocuments as $item)
                <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                    <div class="flex flex-col">
                        <span class="font-semibold">Документ:</span>
                        <span>{{ $item->title }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Описание:</span>
                        <span>{{ $item->description }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Тип:</span>
                        <span>{{ $item->type }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Категория:</span>
                        <div class="flex flex-row space-x-2">
                            @foreach(json_decode($item->category, true) as $value)
                            <span>{{ $value }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Действия:</span>
                        <div class="flex flex-row space-x-2">
                            <a href="{{ asset('storage/'. $item->file_path) }}" target="_blank" rel="noopener noreferrer" class="w-auto grow btn-download" data-tippy-content="Скачать файл">
                                <i class="fa-solid fa-paperclip"></i>
                            </a>
                            @can('create-edit-regulatory-document')
                            <button wire:click="edit({{ $item->id }})" class="w-auto grow btn-edit" data-tippy-content="Редактировать">
                                <span wire:loading.remove wire:target="edit({{ $item->id }})">
                                    <i class="fa-solid fa-pen"></i>
                                </span>
                                <span wire:loading wire:target="edit({{ $item->id }})">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </span>
                            </button>
                            <button onclick="confirmDestroy('{{ $item->id }}')" class="w-auto grow btn-destroy" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-2">
                {{ $regulatoryDocuments->links() }}
            </div>
            @else
            <x-alert type="info" title="Информация" message="Список документов пуст" />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="fecthData, search">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных</span>
            </div>
        </div>
    </div>

    <!-- Modal dialog to create or edit record -->
    <x-dialog-modal wire:model="isCreateOrEditModalVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                Форма {{ ($isEdit) ? 'редактирования' : 'добавления' }} записи
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="title" class="font-semibold">Наименование документа<span class="ml-1 text-red-400">*</span></label>
                        <input wire:model="title" type="text" id="title" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('title') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description" class="font-semibold">Описание документа</label>
                        <textarea wire:model="description" name="description" id="description" cols="5" rows="2" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="type" class="font-semibold">Тип документа<span class="ml-1 text-red-400">*</span></label>
                        <select wire:model="type" name="type" id="type" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="">Выберите из списка тип документа</option>
                            <option value="laws">Законы</option>
                            <option value="decrees">Постановления</option>
                            <option value="orders">Приказы</option>
                            <option value="regulations">Регламенты</option>
                            <option value="standards">Стандарты</option>
                            <option value="instructions">Инструкции</option>
                            <option value="rules">Правила</option>
                            <option value="gost">Государственные стандарты (ГОСТ)</option>
                            <option value="sanitary_norms">Санитарные нормы</option>
                            <option value="technical_regulations">Технические регламенты</option>
                        </select>
                        @error('type') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="type" class="font-semibold">Категория документа<span class="ml-1 text-red-400">*</span></label>
                        @foreach($categories as $item)
                        <div class="mr-2">
                            <label for="category-{{ $loop->iteration }}" class="flex space-x-1 items-center">
                                <input wire:model="category" value="{{ $item }}" type="checkbox" id="category-{{ $loop->iteration }}" class="mr-1">{{ $item }}
                            </label>
                        </div>
                        @endforeach
                        @error('category') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <label for="file-id" class="font-semibold">Файл<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="file" type="file" name="file" id="file-id-{{ $rand }}" class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.pdf, *.doc, *.docx</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 9 МБ</span>
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
</script>
@endpush