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

    @if(session()->has('message-error') && $isAlertVisible)
    <div class="flex items-center justify-between bg-red-500 border-t-4 border-red-700 rounded shadow-xl p-4 text-white" role="alert">
        <div class="flex items-center">
            <i class="fa-solid fa-xmark fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                {{ session('message-error') }}
            </div>
        </div>
        <button wire:click="alertVisible"><i class="fa-solid fa-xmark fal-lg"></i></button>
    </div>
    @endif

    <div class="flex flex-row space-x-1">
        <span class="bg-purple-600 rounded px-2 py-1 text-sm text-white">Лекции</span>
        <span class="bg-sky-600 rounded px-2 py-1 text-sm text-white">Лабораторные</span>
        <span class="bg-yellow-600 rounded px-2 py-1 text-sm text-white">Практические</span>
    </div>

    <div class="flex flex-row space-x-1">
        <span class="bg-green-600 rounded px-2 py-1 text-sm text-white">
            <label for="" class="flex items-center">
                <input class="mr-2" type="checkbox" checked disabled readonly>Присутствовал
            </label>
        </span>
        <span class="bg-red-600 rounded px-2 py-1 text-sm text-white">
            <label for="" class="flex items-center">
                <input class="mr-2" type="checkbox" disabled readonly>Отсутствовал
            </label>
        </span>
    </div>

    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
            <table class="w-auto bg-white shadow-md border text-xs">
                <tbody>
                    <tr class="z-10 sticky top-0 border">
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="sticky left-0 w-auto bg-white border px-2 py-2 font-semibold">ФИО</td>
                        <td class="w-20 border px-2 py-2 text-center font-semibold">№ зачетки</td>
                        @foreach($dates as $dateAndTime)
                        <td class="w-20 border border-t-4 border-r-4
                            @if($dateAndTime->lesson_type == 1) 
                                border-t-purple-600 
                            @elseif($dateAndTime->lesson_type == 2) 
                                border-t-sky-600
                            @else 
                                border-t-yellow-600
                            @endif
                            px-2 py-2 text-center font-semibold">
                            {{ \Carbon\Carbon::parse($dateAndTime->lesson_date)->format('d.m.Y') }} {{ $dateAndTime->lesson_time }}
                        </td>
                        @endforeach
                        <td class="w-20 border px-2 py-2 text-center font-semibold">
                            {{ $currentData }} {{ $currentTime }}
                            <button wire:click="creatingAndEditingModalFormVisible(false)" class="w-full bg-green-500 hover:bg-green-600 rounded p-1 text-white">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </td>
                    </tr>
                    @foreach($contingent as $key => $value)
                    <tr class="{{ ($loop->iteration % 2 === 0) ? 'bg-gray-100' : '' }} hover:bg-gray-300 border">
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="z-0 sticky left-0 w-auto bg-white border px-2 py-2">{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}</td>
                        <td class="w-20 border px-2 py-2 text-center">{{ $value->ticket_number }}</td>
                        @foreach($journal as $item)
                        @if($value->user_id == $item->student_id)
                        <td class="w-20 border px-2 py-2 text-center">
                            <input wire:model="status.{{ $item->id }}" wire:change="setStatus({{ $item->id }}, {{ $value->user_id }})" type="checkbox" id="status-{{ $item->id }}">
                        </td>
                        @endif
                        @endforeach
                        <td class="w-20 border px-2 py-2 text-center"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Creating And Editing Modal Form -->
    <x-dialog-modal wire:model="isCreatingAndEditingModalFormVisible">
        <form>
            @csrf
            <x-slot name="title">
                {{ (!$isEdit) ? __('Форма создания записи') : __('Форма редактирования записи') }}
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="w-full flex flex-col">
                        <label for="lesson-date-id" class="font-semibold">Дата занятия</label>
                        <input wire:model="lesson_date" type="text" id="lesson-date-id" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" tabindex="-1">
                    </div>
                    <div class="flex flex-col">
                        <label for="lesson-type-id" class="font-semibold">Тип занятия<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="lesson_type" name="lesson_type" id="lesson-type-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($listfOfLessonType as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="lesson-time-id" class="font-semibold">Время занятия<span class="ml-1 text-red-500">*</span></label>
                        <select wire:model="lesson_time" name="lesson_time" id="lesson-time-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($listOfLessonTime as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isCreatingAndEditingModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click.prevent="{{ (!$isEdit) ? 'store' : 'update' }}" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Создать') : __('Обновить') }}
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    var pickerLessonDate = new Pikaday({
        field: document.getElementById('lesson-date-id'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            @this.set('lesson_date', document.getElementById('lesson-date-id').value);
        }
    });
</script>
@endpush