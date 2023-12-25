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

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <button type="button" wire:click="achievementModalVisible(false)" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить индивидуальное достижение
        </button>
        <button type="button" wire:click="descriptionCategoriesModalVisible" class="w-full md:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-circle-question fa-lg"></i>
            Описание категорий
        </button>
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!$achievements->isEmpty())
        <!-- PC, notebook, tablet -->
        <div class="hidden md:block">
            <table class="w-full bg-white border shadow-md text-xs">
                <tbody>
                    <tr class="bg-gray-100">
                        <td class="w-14 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-1/4 border px-2 py-2 font-semibold">Категория</td>
                        <td class="w-1/4 border px-2 py-2 font-semibold">Наименование</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Описание</td>
                        <td class="w-24 border px-2 py-2 text-center font-semibold">Год</td>
                        <td class="w-36 border px-2 py-2 text-center font-semibold">Действия</td>
                    </tr>
                    @foreach($achievements as $item)
                    <tr>
                        <td class="w-14 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-1/4 border px-2 py-2">
                            @if($item->achievement_category == 1)
                            Образование и профессиональное развитие
                            @elseif($item->achievement_category == 2)
                            Научно-исследовательская деятельность
                            @elseif($item->achievement_category == 3)
                            Учебно-методическая деятельность
                            @elseif($item->achievement_category == 4)
                            Общественная и творческая деятельность
                            @elseif($item->achievement_category == 5)
                            Профессиональные достижения и проекты
                            @else
                            Прочее
                            @endif
                        </td>
                        <td class="w-1/4 border px-2 py-2">{{ $item->name }}</td>
                        <td class="w-auto border px-2 py-2">
                            @if(empty($item->description))
                            Описание отсутствует
                            @else
                            {{ $item->description }}
                            @endif
                        </td>
                        <td class="w-24 border px-2 py-2 text-center">{{ $item->year }}</td>
                        <td class="w-36 border px-2 py-2 text-center">
                            <a href="storage/{{ $item->file_path }}" target="_blank" class="mr-1 bg-violet-500 hover:bg-violet-600 rounded shadow-xl px-2 py-1.5 text-xs text-white" title="Скачать файл">
                                <i class="fa-solid fa-paperclip"></i>
                            </a>
                            <button wire:click="edit({{ $item->id }}, true)" class="bg-sky-500 hover:bg-sky-600 rounded shadow-xl px-2 py-1.5 text-xs text-white" title="Редактировать запись">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button wire:click="confirmDeleteRecordModalVisible({{ $item->id }})" class="bg-red-500 hover:bg-red-600 rounded shadow-xl px-2 py-1.5 text-xs text-white" title="Удалить запись">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-2">
                {{ $achievements->links() }}
            </div>
        </div>
        <!-- Mobile -->
        <div class="block md:hidden flex flex-col space-y-2">
            @foreach($achievements as $item)
            <div class="flex flex-col space-y-2 border rounded shadow-md p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Категория:</span>
                    @if($item->achievement_category == 1)
                    <span>Образование и профессиональное развитие</span>
                    @elseif($item->achievement_category == 2)
                    <span>Научно-исследовательская деятельность</span>
                    @elseif($item->achievement_category == 3)
                    <span>Учебно-методическая деятельность</span>
                    @elseif($item->achievement_category == 4)
                    <span>Общественная и творческая деятельность</span>
                    @elseif($item->achievement_category == 5)
                    <span>Профессиональные достижения и проекты</span>
                    @else
                    <span>Прочее</span>
                    @endif
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Наименование:</span>
                    <span>{{ $item->name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Описание:</span>
                    <span>{{ $item->description }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Год:</span>
                    <span>{{ $item->year }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="grid grid-cols-3 gap-2">
                        <a href="storage/{{ $item->file_path }}" target="_blank" class="w-auto grow bg-violet-500 hover:bg-violet-600 rounded shadow-xl px-2 py-1.5 text-xs text-white text-center" title="Скачать файл">
                            <i class="fa-solid fa-paperclip"></i>
                        </a>
                        <button wire:click="edit({{ $item->id }}, true)" title="Редактировать запись" class="w-auto grow bg-sky-500 hover:bg-sky-600 rounded shadow-xl px-2 py-1 text-white">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button wire:click="confirmDeleteRecordModalVisible({{ $item->id }})" title="Удалить запись" class="w-auto grow bg-red-500 hover:bg-red-600 rounded shadow-xl px-2 py-1 text-white">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="mt-2">
                {{ $achievements->links() }}
            </div>
        </div>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует! Вы еще не загружали индивидуальные достижения.
            </div>
        </div>
        @endif
    </div>

    <!-- Modal dialog to add or edit achievement -->
    <x-dialog-modal wire:model="isAchievementModalVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                Форма {{ (!$isEdit) ? 'добавления' : 'редактирования' }} индивидуального достижения
            </x-slot>

            <x-slot name="content">
                <div class="w-full flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="achievement-category-id" class="font-semibold">Категории<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="achievementCategory" name="achievementCategory" id="achievement-category-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите категорию</option>
                            @foreach($achievementCategories as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('achievementCategory') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="name-id" class="font-semibold">Наименование<span class="ml-1 text-red-500">*</span></label>
                        <input wire:model="name" type="text" name="name" id="name-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('name') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description-id" class="font-semibold">Описание</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col">
                        <label for="year-id" class="font-semibold">Год<span class="ml-1 text-red-500">*</span></label>
                        <input wire:model="year" type="number" name="year" id="year-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('year') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <label for="file-id" class="font-semibold">Файл<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="file" type="file" name="file" id="file-id-{{ $rand }}" class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.pdf, *.jpg, *.bmp, *.png</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 3 МБ</span>
                        <div wire:loading wire:target="file" class="text-green-500 font-semibold">Идет загрузка файла ...</div>
                        @error('file') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        @if($isEdit)
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
                <button type="button" wire:click="$toggle('isAchievementModalVisible')" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    Закрыть
                </button>
                @if(!$isEdit)
                <button type="button" wire:click.prevent="store" wire:loading.class="disabled" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    Добавить
                </button>
                @else
                <button type="button" wire:click.prevent="update" wire:loading.class="disabled" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    Обновить
                </button>
                @endif
            </x-slot>
        </form>
    </x-dialog-modal>

    <!-- Modal dialog to show description categories -->
    <x-dialog-modal wire:model="isDescriptionCategoriesModalVisible">
        <x-slot name="title">
            Описание категорий индивидуальных достижений
        </x-slot>

        <x-slot name="content">
            <div class="w-full flex flex-col">
                <p class="font-semibold">Образование и профессиональное развитие:</p>
                <ul class="ml-6 list-disc">
                    <li>Дипломы и сертификаты об образовании</li>
                    <li>Академические заслуги и награды</li>
                    <li>Профессиональные квалификации и тренинги</li>
                </ul>
                <p class="font-semibold">Научно-исследовательская деятельность:</p>
                <ul class="ml-6 list-disc">
                    <li>Научные статьи и публикации</li>
                    <li>Монографии и исследовательские работы</li>
                    <li>Участие в конференциях и симпозиумах</li>
                </ul>
                <p class="font-semibold">Учебно-методическая деятельность:</p>
                <ul class="ml-6 list-disc">
                    <li>Учебные пособия и учебные материалы</li>
                    <li>Методические разработки и презентации</li>
                </ul>
                <p class="font-semibold">Общественная и творческая деятельность:</p>
                <ul class="ml-6 list-disc">
                    <li>Благодарности и грамоты за общественное участие</li>
                    <li>Творческие произведения и художественные работы</li>
                    <li>Участие в социальных и культурных мероприятиях</li>
                </ul>
                <p class="font-semibold">Профессиональные достижения и проекты:</p>
                <ul class="ml-6 list-disc">
                    <li>Профессиональные рекомендации и отзывы</li>
                    <li>Результаты конкурсов и достижения в профессиональной сфере</li>
                </ul>
                <p class="font-semibold">Прочее:</p>
                <ul class="ml-6 list-disc">
                    <li>Документы, которые не подходят ни в одну из вышеперечисленных категорий</li>
                </ul>
            </div>
        </x-slot>

        <x-slot name="footer">
            <button type="button" wire:click="$toggle('isDescriptionCategoriesModalVisible')" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                Закрыть
            </button>
        </x-slot>
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