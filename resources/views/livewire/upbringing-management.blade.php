<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    @can('add-educational-and-youth-policy-events')
    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
        <button wire:click="modalVisible(false)" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-2 text-xs text-left text-green-400 hover:text-white font-bold">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить запись
        </button>
    </div>
    @endcan

    @if(!is_null($upbringingEvents) && count($upbringingEvents) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Наименование мероприятия</th>
                    <th scope="col" class="w-56 px-2 py-2 text-left font-semibold">Описание</th>
                    <th scope="col" class="w-56 px-2 py-2 text-left font-semibold">Ответственное лицо</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Дата проведения</th>
                    <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Учебный год</th>
                    <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Полугодие</th>
                    <th scope="col" class="w-44 px-2 py-2 text-left font-semibold">Ссылка</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Файл(ы)</th>
                    @can('add-educational-and-youth-policy-events')
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($upbringingEvents as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->name }}</td>
                    <td class="w-56 px-2 py-2">{{ $value->description }}</td>
                    <td class="w-56 px-2 py-2">{{ $value->fio }}</td>
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
                    @can('add-educational-and-youth-policy-events')
                    <td class="w-32 px-2 py-2 text-center">
                        <div class="flex flex-row items-center justify-center space-x-1">
                            <button wire:click="modalVisible(true, {{ $value->id }})" class="w-9 bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="confirmDestroy('{{ $value->id }}')" class="w-9 bg-white hover:bg-red-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 px-2 py-1 text-xs text-center text-red-400 hover:text-white" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-2">
        {{ $upbringingEvents->links() }}
    </div>
    @else
    <x-alert type="info" title="Информация" message="Список воспитательных мероприятий отсутствует" />
    @endif

    <x-dialog-modal wire:model="showModal">
        <form>
            @csrf
            <x-slot name="title">
                Форма {{ (!$isEdit) ? __('добавления записи') : __('редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
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
                        <input wire:model="date" type="text" id="date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
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
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('showModal')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button id="modal-action-btn" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Добавить') : __('Обновить') }}
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
        if (!@this.isEdit) {
            showToast('store');
        } else {
            showToast('update');
        }
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

    function confirmDestroy(id) {
        if (confirm('Вы уверены, что хотите удалить данную запись?')) {
            showToast('destroy', id);
        }
    }

    var pickerСheckInDate = new Pikaday({
        field: document.getElementById('date'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            @this.set('date', document.getElementById('date').value);
        }
    });
</script>
@endpush