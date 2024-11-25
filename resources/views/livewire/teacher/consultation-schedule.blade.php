<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div class="flex flex-col md:flex-row md:justify-end space-y-2 md:space-y-0">
            <button wire:click="show" class="w-auto md:w-36 btn-add">
                <span wire:loading.remove wire:target="show">
                    <i class="fa-solid fa-circle-plus"></i>
                    Добавить запись
                </span>
                <span wire:loading wire:target="show">
                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                </span>
            </button>
        </div>

        @if(!is_null($consultationSchedules) && count($consultationSchedules) != 0)
        @else
        <x-alert type="info" title="Информация" message="График консультаций еще не заполен. Для добавления новой записи нажмите на кнопку «Добавить запись»" />
        @endif

    </div>

    <!-- Modal dialog to create or edit record -->
    <x-dialog-modal wire:model="isCreateOrEditModalVisible">
        <form>
            @csrf
            <x-slot name="title">
                Форма {{ ($isEdit) ? 'редактирования' : 'добавления' }} записи
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="discipline-id" class="font-semibold">Дисциплина/Практика<span class="text-red-500">*</span></label>
                        <select wire:model="disciplineId" wire:change="setDisciplineId" name="disciplineId" id="discipline-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="0">Выберите дисциплину/практику из списка</option>
                            @foreach($listOfDisciplines as $value)
                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                            @endforeach
                        </select>
                        @error('disciplineId') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <div class="flex flex-row">
                            @foreach($listOfGroups as $key => $value)
                            <div class="mr-2">
                                <label for="group-{{ $value->id }}" class="flex space-x-1 items-center">
                                    <input wire:model="groups" value="{{ $value->id }}" type="checkbox" id="group-{{ $value->id }}" class="mr-1">{{ $value->group_name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('groups') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="consultation-date" class="font-semibold">Дата консультации<span class="text-red-500">*</span></label>
                        <input wire:model="consultationDate" type="date" name="consultationDate" id="consultation-date" class="w-auto border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('consultationDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="consultation-time" class="font-semibold">Период консультации (время)<span class="text-red-500">*</span></label>
                            <select wire:model="consultationTime" name="consultationTime" id="consultation-time" class="w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">Выберите период консультации из списка</option>
                                @foreach($listOfLessonTime as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('consultationTime') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col">
                            <label for="room" class="font-semibold">Аудитория<span class="text-red-500">*</span></label>
                            <select wire:model="room" name="room" id="room" class="w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">Выберите аудиторию из списка</option>
                                @foreach($listOfRooms as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('room') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="description" class="font-semibold">Комментарий</label>
                        <textarea wire:model="description" name="description" id="description" cols="5" rows="5" class="w-full border border-gray-300 rounded px-2 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
                        @error('description') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
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