<div class="flex flex-col space-y-2">

    @if(!is_null($publications) && count($publications) != 0)
    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-56 px-2 py-2 text-left font-semibold">ФИО автора</th>
                    <th scope="col" class="w-56 px-2 py-2 text-left font-semibold">Наименование</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Выходные данные</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Год издания</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Объём в стр.</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Статус</th>
                    <th scope="col" class="w-44 px-2 py-2 text-left font-semibold">Ссылка в elibrary</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Файл</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($publications as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-56 px-2 py-2">{{ $value->author }}</td>
                    <td class="w-56 px-2 py-2">{{ $value->title }}</td>
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
                    <td class="w-44 px-2 py-2">
                        @if(!is_null($value->link_elibrary))
                        <a href="{{ $value->link_elibrary }}" class="text-blue-400 hover:underline" target="_blank">{{ $value->link_elibrary }}</a>
                        @else
                        <span>-</span>
                        @endif
                    </td>
                    <td class="w-16 px-2 py-2 text-center">
                        @if(!is_null($value->file_path))
                        <a href="storage/{{ $value->file_path }}" class="text-blue-400 hover:underline" target="_blank">Смотреть</a>
                        @else
                        <span>-</span>
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        <button wire:click="downloadBibliographicDescription({{ $value->id }})" class="bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white" title="" data-tippy-content="Библиографическое описание">
                            <i class="fa-solid fa-download"></i>
                        </button>
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
                <span class="font-semibold">ФИО автора:</span>
                <span>{{ $value->author }}</span>
            </div>
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
                @if(!is_null($value->link_elibrary))
                <a href="{{ $value->link_elibrary }}" class="text-blue-400 hover:underline" target="_blank">{{ $value->link_elibrary }}</a>
                @else
                <span>-</span>
                @endif
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Файл:</span>
                @if(!is_null($value->file_path))
                <a href="storage/{{ $value->file_path }}" class="text-blue-400 hover:underline" target="_blank">Смотреть</a>
                @else
                <span>-</span>
                @endif
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Действия:</span>
                <div class="flex flex-row space-x-2">
                    <button wire:click="downloadBibliographicDescription({{ $value->id }})" class="w-auto grow bg-white hover:bg-sky-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-sky-400 px-2 py-1 text-xs text-center text-sky-400 hover:text-white font-bold" title="" data-tippy-content="Библиографическое описание">
                        <i class="fa-solid fa-download"></i>
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
    <x-alert type="info" title="Информация" message="Список научных трудов сотрудников отсутствует" />
    @endif
</div>