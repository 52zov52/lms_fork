<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
        <button wire:click="modalVisible(false)" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-2 text-xs text-left text-green-400 hover:text-white font-bold">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить запись
        </button>
    </div>

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
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Файл</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upbringingEvents as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->name }}</td>
                    <td class="w-56 px-2 py-2">{{ $value->description }}</td>
                    <td class="w-16 px-2 py-2">{{ \Carbon\Carbone::parse($value->data)->format('d.m.Y') }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->academic_year }}</td>
                    <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                    <td class="w-16 px-2 py-2 text-center">
                        -
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        <div class="grid gap-1 grid-cols-2">
                            <button wire:click="edit({{ $value->id }})" class="bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="confirmDestroy('{{ $value->id }}')" class="bg-white hover:bg-red-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 px-2 py-1 text-xs text-center text-red-400 hover:text-white" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
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