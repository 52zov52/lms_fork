<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    @can('access-to-edit-elibrary')
    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 md:justify-end">
        <button wire:click="setEntryModalVisible(false)" class="bg-white hover:bg-green-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 px-2 py-2 text-xs text-left text-green-400 hover:text-white font-bold">
            <i class="fa-solid fa-circle-plus fa-lg"></i>
            Добавить запись
        </button>
    </div>
    @endcan

    @if(!is_null($elibraries) && count($elibraries) != 0)
    <table class="w-full text-xs lg:text-sm">
        <tbody>
            @foreach($elibraries as $elibrary)
            <tr class="bg-white dark:bg-slate-800 ">
                <td class="w-auto px-2 py-2">
                    <div class="flex flex-col space-y-2 md:flex-row md:justify-between md:space-y-0">
                        <a href="{{ $elibrary->url }}" class="text-blue-500 font-semibold hover:underline">{{ $elibrary->name }}</a>
                        @can('access-to-edit-elibrary')
                        <div class="grid gap-1 grid-cols-2">
                            <button wire:click="setEntryModalVisible(true, {{ $elibrary->id }})" class="w-auto bg-white hover:bg-orange-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-orange-400 px-2 py-1 text-xs text-center text-orange-400 hover:text-white font-bold" title="" data-tippy-content="Редактировать">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                            <button onclick="confirmDestroy('{{ $elibrary->id }}')" class="w-auto bg-white hover:bg-red-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 px-2 py-1 text-xs text-center text-red-400 hover:text-white font-bold" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        @endcan
                    </div>
                </td>
            </tr>
            <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                <td class="w-auto px-2 py-2 text-justify">{!! $elibrary->description !!}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <x-alert type="info" title="Информация" message="Список электронных библиотечных систем отсутствует" />
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
                    <div class="flex flex-col">
                        <label for="name" class="font-semibold">Наименование ЭБС</label>
                        <input wire:model="name" type="text" name="name" id="name" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('name') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="url" class="font-semibold">Адрес ЭБС в сети интернет</label>
                        <input wire:model="url" type="text" name="url" id="url" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('url') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description" class="font-semibold">Описание</label>
                        <textarea wire:model="description" name="description" id="description" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-red-400 font-semibold">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isEntryModalVisible')" type="button" class="w-full sm:w-auto bg-white hover:bg-gray-400 border rounded border-gray-400 px-2 py-2 text-xs text-gray-400 hover:text-white font-bold">
                    {{ __('Отменить') }}
                </button>
                <button id="btn-action" type="button" class="ml-2 w-full sm:w-auto bg-white hover:bg-blue-400 border rounded border-blue-400 px-2 py-2 text-xs text-blue-400 hover:text-white font-bold">
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