<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    @if(session()->has('message-error'))
    <div class="flex items-center bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-error') }}
        </div>
    </div>
    @endif

    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0 items-center">
        <button wire:click="creatingAndEditingModalFormVisible(false)" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">
            Добавить запись
        </button>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        @if(!$operatingActivity->isEmpty())
        <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
            <tbody>
                <tr class="bg-gray-100">
                    <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                    <td class="w-auto border px-2 py-2 font-semibold">Направление деятельности</td>
                    <td class="w-auto border px-2 py-2 font-semibold">Исполнитель</td>
                    <td class="w-auto border px-2 py-2 font-semibold">Задачи</td>
                    <td class="w-auto border px-2 py-2 text-center font-semibold">Статус</td>
                    <td class="w-auto border px-2 py-2 text-center font-semibold">Действия</td>
                </tr>
                <tr class="bg-gray-100">
                    <td class="w-12 border px-2"></td>
                    <td class="w-auto border px-2">
                        <input class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></td>
                    <td class="w-auto border px-2 py-1">
                        <input wire:model.live="fio" type="text" name="fio" id="fio-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </td>
                    <td class="w-auto border px-2">
                        <input class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></td>
                    <td class="w-auto border px-2">
                        <input class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></td>
                    <td class="w-auto border px-2"></td>
                </tr>
                @foreach($operatingActivity as $value)
                <tr>
                    <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto border px-2 py-2">{{ $value->activity_direction }}</td>
                    <td class="w-auto border px-2 py-2">{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}</td>
                    <td class="w-auto border px-2 py-2">{{ $value->task }}</td>
                    <td class="w-auto border px-2 py-2 text-center">
                        @if($value->status == 1)
                        <span class="w-24 bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">В работе</span>
                        @elseif($value->status == 2)
                        <span class="w-28 bg-blue-500 rounded px-2 py-1 text-xs text-white font-bold">На утверждение</span>
                        @else
                        <span class="w-24 bg-green-500 rounded px-2 py-1 text-xs text-white font-bold">Исполнено</span>
                        @endif
                    </td>
                    <td class="w-auto border px-2 py-2 text-center">
                        <button wire:click="creatingAndEditingModalFormVisible(true, {{ $value->id }})" class="text-sky-500" title="Редактировать">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <button wire:click="destroy({{ $value->id }})" class="text-red-500" title="Удалить">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $operatingActivity->links() }}
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                Информация отсутсвует! Для добавления записи нажмите на кнопку "Добавить запись".
            </div>
        </div>
        @endif
    </div>

    <!-- Creating And Editing Modal Form -->
    <x-dialog-modal wire:model="isCreatingAndEditingModalFormVisible">
        <form enctype="multipart/form-data">
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Форма создания новой записи') : __('Форма редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="activity-direction-id">Направление деятельности</label>
                        <input wire:model="activityDirection" type="text" name="activityDirection" id="activity-direction-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('activityDirection') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="task-id">Задача</label>
                        <input wire:model="task" type="text" name="task" id="task-id" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('task') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="description-id">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description-id" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex flex-col">
                        <label for="status-id"></label>
                        <select wire:model="status" name="status" id="status-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите статус</option>
                            <option value="1">В работе</option>
                            <option value="2">На утверждение</option>
                            <option value="3">Исполнено</option>
                        </select>
                        @error('status') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <input wire:model="file" type="file" name="file" id="file-id" class="w-full">
                        @error('file') <span class="text-red-500">{{ $message }}</span> @enderror
                        <div wire:loading wire:target="file">Идет загрузка файла ...</div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isCreatingAndEditingModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="{{ (!$isEdit) ? 'store' : 'update' }}" type="button" class="ml-2 w-full sm:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Создать') : __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>