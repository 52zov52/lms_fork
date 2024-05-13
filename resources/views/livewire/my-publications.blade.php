<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
        <button wire:click="setEntryModalVisible(false)" class="bg-green-400 hover:bg-green-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 hover:border-green-500 dark:border-slate-400/20 px-2 py-2 text-xs text-left text-white dark:text-green-400 font-bold">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить запись
        </button>
        <button wire:click="print()" class="bg-blue-400 hover:bg-blue-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 hover:border-blue-500 dark:border-slate-400/20 px-2 py-2 text-xs text-left text-white dark:text-blue-400 font-bold">
            <i class="fa-solid fa-print fa-lg"></i>
            Печать списка трудов
        </button>
    </div>

    @if(!is_null($publications) && count($publications) != 0)
    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-60 px-2 py-2 font-semibold">Наименование</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">Выходные данные</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Год издания</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Объём в стр.</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Статус</th>
                    <th scope="col" class="w-48 px-2 py-2 font-semibold">Ссылка в elibrary</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($publications as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-60 px-2 py-2">{{ $value->title }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->output_data }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->year_publishing }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->volume_full_on_pages }}</td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if($value->status == 1)
                        <span class="font-bold cursor-pointer">РИНЦ</span>
                        @elseif($value->status == 2)
                        <span class="font-bold cursor-pointer">ВАК</span>
                        @elseif($value->status == 3)
                        <span class="font-bold cursor-pointer">WoS</span>
                        @elseif($value->status == 4)
                        <span class="font-bold cursor-pointer">Scopus</span>
                        @elseif($value->status == 5)
                        <span class="font-bold cursor-pointer">ISBN</span>
                        @else
                        <span class="font-bold cursor-pointer">-</span>
                        @endif
                    </td>
                    <td class="w-48 px-2 py-2">
                        <a href="{{ $value->link_elibrary }}" target="_blank">{{ $value->link_elibrary }}</a>
                    </td>
                    <td class="w-16 px-2 py-2 text-center">
                        <div class="grid gap-1 grid-cols-2">
                            @if(!is_null($value->file_path))
                            <a href="storage/{{ $value->file_path }}" target="_blank" class="bg-blue-400 hover:bg-blue-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 hover:border-blue-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-blue-400 font-bold" title="" data-tippy-content="Скачать">
                                <i class="fa-solid fa-paperclip"></i>
                            </a>
                            @endif
                            <button wire:click="setEntryModalVisible(true, {{ $value->id }})" class="bg-orange-400 hover:bg-orange-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 hover:border-orange-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-orange-400 font-bold" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="confirmDestroy('{{ $value->id }}')" class="bg-red-400 hover:bg-red-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 hover:border-red-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-red-400 font-bold" title="" data-tippy-content="Удалить">
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
        @foreach($publications as $value)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
            <div class="flex flex-col">
                <span class="font-semibold">Наименование:</span>
                <span>{{ $value->title }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Выходные данные:</span>
                <span>{{ $value->output_data }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Год издания:</span>
                <span>{{ $value->year_publishing }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Объём в стр.:</span>
                <span>{{ $value->volume_full_on_pages }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Статус:</span>
                @if($value->status == 1)
                <span class="font-bold cursor-pointer">РИНЦ</span>
                @elseif($value->status == 2)
                <span class="font-bold cursor-pointer">ВАК</span>
                @elseif($value->status == 3)
                <span class="font-bold cursor-pointer">WoS</span>
                @elseif($value->status == 4)
                <span class="font-bold cursor-pointer">Scopus</span>
                @elseif($value->status == 5)
                <span class="font-bold cursor-pointer">ISBN</span>
                @else
                <span class="font-bold cursor-pointer">-</span>
                @endif
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Ссылка в elibrary:</span>
                <span>{{ $value->link_elibrary }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Действия:</span>
                <div class="flex flex-row space-x-2">
                    @if(!is_null($value->file_path))
                    <a href="storage/{{ $value->file_path }}" target="_blank" class="w-auto grow bg-blue-400 hover:bg-blue-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 hover:border-blue-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-blue-400 font-bold" title="" data-tippy-content="Скачать">
                        <i class="fa-solid fa-paperclip"></i>
                    </a>
                    @endif
                    <button wire:click="setEntryModalVisible(true, {{ $value->id }})" class="w-auto grow bg-orange-400 hover:bg-orange-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 hover:border-orange-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-orange-400 font-bold" title="" data-tippy-content="Редактировать">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button onclick="confirmDestroy('{{ $value->id }}')" class="w-auto grow bg-red-400 hover:bg-red-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 hover:border-red-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-red-400 font-bold" title="" data-tippy-content="Удалить">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-2">
        {{ $publications->links() }}
    </div>
    @else
    <x-alert type="info" title="Информация" message="Список научных трудов отсутствует" />
    @endif

    <!-- Entry Modal -->
    <x-dialog-modal wire:model="isEntryModalVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Добавление записи') : __('Редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="category" class="font-semibold">Категория<span class="ml-1 text-red-400">*</span></label>
                            <select wire:model="category" name="category" id="category" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Выберите категорию</option>
                                <option value="1">Научная работа</option>
                                <option value="2">Учебно-методическая работа</option>
                                <option value="3">Авторское свидетельство, диплом, патент, лицензия</option>
                            </select>
                            @error('category') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="status" class="font-semibold">Статус<span class="ml-1 text-red-400">*</span></label>
                            <select wire:model="status" name="status" id="status" class="w-full border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="0">Выберите статус</option>
                                <option value="1">Индекс РИНЦ</option>
                                <option value="2">Журнал ВАК</option>
                                <option value="3">Система Web of Science</option>
                                <option value="4">Система Scopus</option>
                                <option value="5">Индекс ISBN</option>
                                <option value="6">Статус отсутствует</option>
                            </select>
                            @error('status') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="title" class="font-semibold">Наименование<span class="ml-1 text-red-400">*</span></label>
                        <input wire:model="title" type="text" id="title" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('title') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="output_data" class="font-semibold">Выходные данные<span class="ml-1 text-red-400">*</span></label>
                        <textarea wire:model="output_data" name="output_data" id="output_data" cols="5" rows="2" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" placeholder="Название журнала или сборника. – Год издания. – Том номер (если есть). – Номер страницы (если есть)."></textarea>
                        @error('output_data') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="year_publishing" class="font-semibold">Год издания<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="year_publishing" type="text" id="year_publishing" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('year_publishing') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="volume_full_on_pages" class="font-semibold">Объём (в страницах)<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="volume_full_on_pages" type="text" id="volume_full_on_pages" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @error('volume_full_on_pages') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="name_co_authors" class="font-semibold">ФИО соавторов</label>
                        <textarea wire:model="name_co_authors" name="name_co_authors" id="name_co_authors" cols="5" rows="2" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('name_co_authors') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="link_elibrary" class="font-semibold">Ссылка в elibrary</label>
                        <input wire:model="link_elibrary" type="text" id="link_elibrary" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('link_elibrary') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="inline-flex py-2">
                        <label for="is-university-affiliation">
                            <input wire:model="is_university_affiliation" type="checkbox" name="is_university_affiliation" id="is-university-affiliation" class="mr-1">Принадлежность научной работы вузу
                        </label>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex">
                            <label for="file-{{ $rand }}" class="font-semibold">Файл<span class="ml-1 text-red-400">*</span></label>
                            <input wire:model="file" type="file" name="file" id="file-{{ $rand }}" class="ml-1 w-full">
                        </div>
                        <span class="text-xs font-semibold">доступные типы файлов: *.pdf</span>
                        <span class="text-xs font-semibold">максимальный объем файла: 3 МБ</span>
                        <div wire:loading wire:target="file" class="text-green-400 font-semibold">Идет загрузка файла ...</div>
                        @error('file') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                        @if($isEdit && !is_null($file_path))
                        <div class="mt-2 flex">
                            <a href="storage/{{ $file_path }}" target="_blank" class="text-blue-400 hover:underline" title="Скачать файл">
                                <i class="fa-solid fa-paperclip"></i> {{ $filename }}
                            </a>
                        </div>
                        <span class="mt-2 w-auto bg-orange-400 rounded p-2 text-white">
                            <i class="fa-solid fa-circle-exclamation"></i> Чтобы заменить файл, загрузите в поле <b>Файл</b> новый файл
                        </span>
                        @endif
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isEntryModalVisible')" type="button" class="w-full sm:w-auto bg-gray-400 hover:bg-gray-500 rounded px-2 py-1 text-white">
                    {{ __('Отменить') }}
                </button>
                <button id="btn-action" type="button" class="ml-2 w-full sm:w-auto bg-blue-400 hover:bg-blue-500 rounded px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Добавить') : __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>

</div>

@push('scripts')
<script>
    const element = document.getElementById('btn-action');
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

    function confirmDestroy(id) {
        if (confirm('Вы уверены, что хотите удалить данную запись?')) {
            showToast('destroy', id);
        }
    }
</script>
@endpush