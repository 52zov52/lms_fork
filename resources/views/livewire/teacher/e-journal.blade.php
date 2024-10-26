<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <h4 class="text-base lg:text-base font-semibold">
            Группа: {{ $group_name }}
        </h4>
        <h4 class="text-base lg:text-base font-semibold">
            Дисциплина: {{ $discipline_name }}
        </h4>

        <div class="w-e-journal h-e-journal border rounded border-slate-400/20 overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="z-10 sticky top-0 bg-slate-200 dark:bg-slate-700 border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                        <th scope="col" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-left font-semibold">ФИО</th>
                        <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">№ зачетки</th>
                        @foreach($lessonInfo as $element)
                        <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">
                            <div class="flex flex-col space-y-1">
                                <span>{{ \Carbon\Carbon::parse($element->lesson_date)->format('d.m.Y') }}</span>
                                <span>{{ $element->lesson_time }}</span>
                                <div class="w-full border rounded">
                                    @if($element->lesson_type == 1)
                                    <span">лек.</span>
                                        @elseif($element->lesson_type == 2)
                                        <span>лаб.</span>
                                        @elseif($element->lesson_type == 3)
                                        <span>практ.</span>
                                        @elseif($element->lesson_type == 4)
                                        <span>к.т.</span>
                                        @elseif($element->lesson_type == 5)
                                        <span>к.т. (тест)</span>
                                        @elseif($element->lesson_type == 6)
                                        <span>зач.</span>
                                        @elseif($element->lesson_type == 7)
                                        <span>диф. зач.</span>
                                        @else
                                        <span>экз.</span>
                                        @endif
                                </div>
                                @if(!$element->is_signature)
                                <div class="grid gap-1 grid-cols-2">
                                    <button wire:click="signature('{{ $element->lesson_date }}', '{{ $element->lesson_time }}', {{ $element->lesson_type }})" class="w-auto btn-signature" data-tippy-content="Подписать">
                                        <span wire:loading.remove wire:target="signature('{{ $element->lesson_date }}', '{{ $element->lesson_time }}', {{ $element->lesson_type }})">
                                            <i class="fa-solid fa-signature"></i>
                                        </span>
                                        <span wire:loading wire:target="signature('{{ $element->lesson_date }}', '{{ $element->lesson_time }}', {{ $element->lesson_type }})">
                                            <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                        </span>
                                    </button>
                                    <button onclick="confirmDestroy('{{ $element->lesson_date }}', '{{ $element->lesson_time }}', '{{ $element->lesson_type }}')" class="w-auto btn-destroy" data-tippy-content="Удалить">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </th>
                        @endforeach
                        <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">
                            {{ $currentData }}
                            <button wire:click="showModalDialog(false)" class="w-full btn-add-center" data-tippy-content="Добавить новый столбец">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contingent as $student)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="z-0 sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2 text-left">{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</td>
                        <td class="w-20 px-2 py-2 text-center">{{ $student->ticket_number }}</td>
                        @foreach($journal as $item)
                        @if($student->user_id == $item->student_id)
                        <td class="w-20 px-2 py-2 text-center {{ ($item->is_signature) ? 'bg-green-200 dark:bg-slate-700' : '' }}">
                            @if(!$item->is_signature)
                            <div wire:loading.remove wire:target="setStatus({{ $item->id }}, {{ $student->user_id }})">
                                <select wire:model="status.{{ $item->id }}" wire:key="{{ $item->id }}" wire:change="setStatus({{ $item->id }}, {{ $student->user_id }})" name="status" id="status-.{{ $item->id }}" class="bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500" @if($item->is_signature) disabled @endif>
                                    @foreach($listOfStatuses as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div wire:loading wire:target="setStatus({{ $item->id }}, {{ $student->user_id }})">
                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </div>
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
                            <div class="w-full h-6 font-bold" data-tippy-content="Неудовлетворительно">0</div>
                            @break
                            @case(5)
                            <div class="w-full h-6 font-bold" data-tippy-content="Неудовлетворительно">1</div>
                            @break
                            @case(6)
                            <div class="w-full h-6 font-bold" data-tippy-content="Неудовлетворительно">2</div>
                            @break
                            @case(7)
                            <div class="w-full h-6 font-bold" data-tippy-content="Удовлетворительно">3</div>
                            @break
                            @case(8)
                            <div class="w-full h-6 font-bold" data-tippy-content="Хорошо">4</div>
                            @break
                            @case(9)
                            <div class="w-full h-6 font-bold" data-tippy-content="Отлично">5</div>
                            @break
                            @case(10)
                            <div class="w-full h-6 font-bold" data-tippy-content="Зачёт">Зач.</div>
                            @break
                            @case(11)
                            <div class="w-full h-6 font-bold" data-tippy-content="Незачёт">Незач.</div>
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
    </div>

    <!-- Modal dialog to create or edit record -->
    <x-dialog-modal wire:model="isShowModalDialog">
        <form>
            @csrf
            <x-slot name="title">
                Форма добавления записи
            </x-slot>

            <x-slot name="content">
                <div class="flex flex-col space-y-2">
                    <div class="flex flex-col">
                        <label for="lesson-date" class="font-semibold">Дата занятия<span class="text-red-400">*</span></label>
                        <input wire:model="lessonDate" type="date" name="lessonDate" id="lesson-date" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('lessonDate') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex flex-col">
                        <label for="lesson-time" class="font-semibold">Время занятия<span class="text-red-400">*</span></label>
                        <select wire:model="lessonTime" name="lessonTime" id="lesson-time" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($listOfLessonTime as $time)
                            <option value="{{ $time }}">{{ $time }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="lesson-type" class="font-semibold">Тип занятия<span class="text-red-400">*</span></label>
                        <select wire:model="lessonType" wire:change="setLessonType" name="lessonType" id="lesson-type" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            @foreach($listfOfLessonType as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="lesson-theme" class="font-semibold">
                            Тема занятия<span class="text-red-400">*</span>
                            <span wire:loading wire:target="setLessonType">
                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </span>
                        </label>
                        <input wire:model="lessonTheme" type="text" name="lessonTheme" id="lesson-theme" class="w-full border border-gray-300 rounded px-2 text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        @error('lessonTheme') <span class="text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <span class="text-red-500">*Поля обязательные для заполнения</span>
                </div>
            </x-slot>

            <x-slot name="footer">
                <button wire:click="$toggle('isShowModalDialog')" type="button" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 rounded shadow-xl px-2 py-1 text-white">
                    {{ __('Закрыть') }}
                </button>
                <button wire:click="store" type="button" class="ml-2 w-full sm:w-auto bg-green-500 hover:bg-green-600 rounded shadow-xl px-2 py-1 text-white">
                    <span wire:loading.remove wire:target="store">
                        {{ __('Создать') }}
                    </span>
                    <span wire:loading wire:target="store">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>

@push('scripts')
<script>
    function confirmDestroy(lessonDate, lessonTime, lessonType) {
        if (confirm('Вы уверены, что хотите удалить данные записи?')) {
            Livewire.dispatch('destroy', {
                lessonDate: lessonDate,
                lessonTime: lessonTime,
                lessonType: lessonType
            })
        }
    }
</script>
@endpush