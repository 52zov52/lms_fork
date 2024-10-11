<div class="flex flex-col space-y-2">
    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    @if(!is_null($upbringingEvents) && count($upbringingEvents) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Наименование мероприятия</th>
                    <th scope="col" class="w-56 px-2 py-2 text-left font-semibold">Описание</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Дата проведения</th>
                    <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Учебный год</th>
                    <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Полугодие</th>
                    <th scope="col" class="w-44 px-2 py-2 text-left font-semibold">Ссылка</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Файл(ы)</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upbringingEvents as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->name }}</td>
                    <td class="w-56 px-2 py-2">{{ $value->description }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($value->date)->format('d.m.Y') }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->academic_year }}</td>
                    <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                    <td class="w-44 px-2 py-2">
                        @if(!is_null($value->link_to_news))
                        <a href="{{ $value->link_to_news }}" class="text-blue-400 hover:underline" target="_blank">{{ $value->link_to_news }}</a>
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
                        <div class="flex flex-row items-center justify-center space-x-1">
                            @if(!is_null($value->event_file))
                            <a href="storage/{{ $value->event_file }}" class="w-9 bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-1 text-xs text-center text-blue-400 hover:text-white" title="" data-tippy-content="Скачать доп. файлы к мероприятию" target="_blank">
                                <i class="fa-solid fa-paperclip"></i>
                            </a>
                            @endif
                            <button wire:click="edit({{ $value->upbringing_event_report_id }})" class="w-9 bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-2">
        {{ $upbringingEvents->links() }}
    </div>
    @else
    <x-alert type="info" title="Информация" message="Список молодежных и воспитательных мероприятий отсутствует" />
    @endif

    <x-dialog-modal wire:model="showModal">
        <form>
            @csrf
            <x-slot name="title">
                Форма {{ __('редактирования записи') }}
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
                <button wire:click="$toggle('showModal')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button id="modal-action-btn" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    const element = document.getElementById('modal-action-btn');
    element.addEventListener('click', function(event) {
        event.preventDefault();
        showToast('update');
    });

    async function showToast(methodName, param = null) {
        await @this.callMethod(methodName, param);;

        if (toast.dataset.notificationType != '') {
            toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

            setTimeout(function() {
                toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
            }, 3500);
        }
    }
</script>
@endpush