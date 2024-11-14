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

        @if(!is_null($academicRetakeSchedules) && count($academicRetakeSchedules) != 0)
        <div class="hidden lg:block border rounded border-slate-400/20">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 font-semibold">№ пп</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Дата</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Время</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Аудитория</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Учебный год</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Полугодие</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($academicRetakeSchedules as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ \Carbon\Carbon::parse($value->retake_date)->format('d.m.Y') }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->retake_time }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->room }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->academic_year }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->half_year }}</td>
                        <td class="w-24 px-2 py-2 text-center">
                            <div class="flex flex-row items-center justify-center space-x-1">
                                <button wire:click="edit({{ $value->id }})" class="w-9 btn-edit" data-tippy-content="Редактировать">
                                    <span wire:loading.remove wire:target="edit({{ $value->id }})">
                                        <i class="fa-solid fa-pen"></i>
                                    </span>
                                    <span wire:loading wire:target="edit({{ $value->id }})">
                                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                    </span>
                                </button>
                                <button onclick="confirmDestroy('{{ $value->id }}')" class="w-9 btn-destroy" title="" data-tippy-content="Удалить">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="lg:hidden flex flex-col space-y-2">
            @foreach($academicRetakeSchedules as $value)
            <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Дата пересдачи:</span>
                    <span>{{ \Carbon\Carbon::parse($value->retake_date)->format('d.m.Y') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Период пересдачи (время):</span>
                    <span>{{ $value->retake_time }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Аудитория:</span>
                    <span>{{ $value->room }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Учебный год:</span>
                    <span>{{ $value->academic_year }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Полугодие:</span>
                    <span>{{ $value->half_year }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="flex flex-row space-x-2">
                        <button wire:click="edit({{ $value->id }})" class="w-auto grow btn-edit" data-tippy-content="Редактировать">
                            <span wire:loading.remove wire:target="edit({{ $value->id }})">
                                <i class="fa-solid fa-pen"></i>
                            </span>
                            <span wire:loading wire:target="edit({{ $value->id }})">
                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </span>
                        </button>
                        <button onclick="confirmDestroy('{{ $value->id }}')" class="w-auto grow btn-destroy" title="" data-tippy-content="Удалить">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $academicRetakeSchedules->links() }}
        </div>
        @else
        <x-alert type="info" title="Информация" message="График пересдач академических задолженностей еще не заполен. Для добавления новой записи нажмите на кнопку «Добавить запись»" />
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
                        <label for="retake-date" class="font-semibold">Дата пересдачи<span class="text-red-500">*</span></label>
                        <input wire:model="retakeDate" type="date" name="retakeDate" id="retake-date" class="w-auto border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('retakeDate') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                        <div class="flex flex-col">
                            <label for="retake-time" class="font-semibold">Период пересдачи (время)<span class="text-red-500">*</span></label>
                            <select wire:model="retakeTime" name="retakeTime" id="retake-time" class="w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                <option value="">Выберите период пересдачи из списка</option>
                                @foreach($listOfLessonTime as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            @error('retakeTime') <span class="text-red-500 font-semibold">{{ $message }}</span> @enderror
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