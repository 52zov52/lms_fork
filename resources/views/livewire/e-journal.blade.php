<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

    <h4 class="text-base lg:text-base font-semibold">{{ $infoAboutGroupAndCourse->group_name }} - {{ $infoAboutGroupAndCourse->discipline_name }}</h4>

    <div class="w-e-journal h-e-journal overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
        <table class="border border-slate-400/20 text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700">
                <tr class="z-10 sticky top-0 bg-slate-200 dark:bg-slate-700 border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold uppercase">№ пп</th>
                    <th scope="col" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 font-semibold uppercase">ФИО</th>
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold uppercase">№ зачетки</th>
                    @foreach($dates as $dateAndTime)
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">
                        {{ \Carbon\Carbon::parse($dateAndTime->lesson_date)->format('d.m.Y') }}
                        {{ $dateAndTime->lesson_time }}
                        @if(!$dateAndTime->is_signature)
                        <div class="flex space-x-1">
                            <button onclick="showToast('setSignature', { lesson_date: '{{ $dateAndTime->lesson_date }}', lesson_time: '{{ $dateAndTime->lesson_time }}', lesson_type: '{{ $dateAndTime->lesson_type }}' })" class="w-8 bg-blue-400 hover:bg-blue-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 hover:border-blue-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-blue-400 font-bold" title="" data-tippy-content="Подписать">
                                <i class="fa-solid fa-signature"></i>
                            </button>
                            <button onclick="showToast('destroy', { lesson_date: '{{ $dateAndTime->lesson_date }}', lesson_time: '{{ $dateAndTime->lesson_time }}', lesson_type: '{{ $dateAndTime->lesson_type }}' })" class="w-8 bg-red-400 hover:bg-red-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-red-400 hover:border-red-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-red-400 font-bold" title="" data-tippy-content="Удалить">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                        @endif
                    </th>
                    @endforeach
                    <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">
                        {{ $currentData }}
                        <button wire:click="creatingAndEditingModalFormVisible(false)" class="w-full bg-green-400 hover:bg-green-500 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-green-400 hover:border-green-500 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-white dark:text-green-400 font-bold" title="" data-tippy-content="Добавить новый столбец">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($contingent as $student)
                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="z-0 sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2">{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</td>
                    <td class="w-20 px-2 py-2 text-center">{{ $student->ticket_number }}</td>
                    @foreach($journal as $item)
                    @if($student->user_id == $item->student_id)
                    <td class="w-20 px-2 py-2 text-center {{ ($item->is_signature) ? 'bg-green-200 dark:bg-slate-700' : '' }}">
                        @if(!$item->is_signature)
                        <select wire:model="status.{{ $item->id }}" wire:change="setStatus({{ $item->id }}, {{ $student->user_id }})" name="status" id="" class="bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($item->is_signature) disabled @endif>
                            @foreach($listOfStatuses as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @else
                        @switch($status[$item->id])
                        @case(1)
                        <div class="w-full h-6 font-bold" data-tippy-content="Присутствовал">.</div>
                        @break
                        @case(2)
                        <div class="w-full h-6 font-bold" data-tippy-content="Отсутсвовал">Н</div>
                        @break
                        @case(3)
                        <div class="w-full h-6 font-bold" data-tippy-content="Отсутсвовал по уважительной причине">НУ</div>
                        @break
                        @case(4)
                        <div class="w-full h-6 font-bold" data-tippy-content="Неудовлетворительно">2</div>
                        @break
                        @case(5)
                        <div class="w-full h-6 font-bold" data-tippy-content="Удовлетворительно">3</div>
                        @break
                        @case(6)
                        <div class="w-full h-6 font-bold" data-tippy-content="Хорошо">4</div>
                        @break
                        @case(7)
                        <div class="w-full h-6 font-bold" data-tippy-content="Отлично">5</div>
                        @break
                        @default
                        -
                        @endswitch
                        @endif
                    </td>
                    @endif
                    @endforeach
                    <td class="w-20 px-2 py-2 text-center"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
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
                    <div class="flex flex-col space-x-0 space-y-2 md:flex-row md:space-x-2 md:space-y-0">
                        <div class="w-full flex flex-col">
                            <label for="lesson-date" class="font-semibold">Дата занятия<span class="ml-1 text-red-500">*</span></label>
                            <input wire:model="lesson_date" type="text" id="lesson-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" tabindex="-1">
                        </div>
                        <div class="w-full flex flex-col">
                            <label for="lesson-time" class="font-semibold">Время занятия<span class="ml-1 text-red-500">*</span></label>
                            <select wire:model="lesson_time" name="lesson_time" id="lesson-time" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @foreach($listOfLessonTime as $time)
                                <option value="{{ $time }}">{{ $time }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full flex flex-col">
                            <label for="lesson-type" class="font-semibold">Тип занятия<span class="ml-1 text-red-500">*</span></label>
                            <select wire:model="lesson_type" wire:change="setTypelesson()" name="lesson_type" id="lesson-type" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                @foreach($listfOfLessonType as $key => $type)
                                <option value="{{ $key }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <label for="lesson-theme" class="font-semibold">Тема занятия<span class="ml-1 text-red-500">*</span></label>
                        <input wire:model="lesson_theme" type="text" id="lesson-theme" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isCreatingAndEditingModalFormVisible')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button id="btn-action" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ (!$isEdit) ? __('Создать') : __('Обновить') }}
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

    var pickerLessonDate = new Pikaday({
        field: document.getElementById('lesson-date'),
        format: 'DD.MM.YYYY',
        onSelect: function() {
            @this.set('lesson_date', document.getElementById('lesson-date').value);
        }
    });
</script>
@endpush