<div class="flex flex-col space-y-2">
    
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-check fa-lg"></i>
        <div class="ml-3 text-base font-medium">
                {{ session('message-success') }}
        </div>
    </div>
    @endif

    <div class="flex items-center bg-sky-500 border-t-4 border-sky-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-info fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            Расписание обновляется два раза в сутки в 08:00 и 18:00 по МСК. Последняя дата и время обновления расписания: {{ $lastUpdateDate }}. По умолчанию всегда отображается текущая неделя.
        </div>
    </div>
    
    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        @role('Teacher')
        <select wire:model="teacher" wire:change="changeTeacher" name="teachers" id="teachers-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите преподавателя</option>
            @foreach($teachers as $value)
            <option value="{{ $value->teacher }}">{{ $value->teacher }}</option>
            @endforeach
        </select>
        @endrole
        <select wire:model="weekRange" wire:change="changeWeekRange" name="weekRange" id="week-range-id" class="w-full md:w-auto border border-gray-300 rounded text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите неделю</option>
            @foreach($weeksRange as $weekRange)
            <option value="{{ $weekRange['date'] }}">{{ $weekRange['date'] }}</option>
            @endforeach
        </select>
        @can('update-schedule')
        <button type="button" wire:click="uploadSchedule" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-2 py-2 text-sm text-left text-white">
            <i class="fa-solid fa-rotate"></i>
            Обновить расписание
        </button>
        @endcan
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!is_null($currentSchedule) && count($currentSchedule) != 0)
        <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
            @foreach($daysOfWeek as $dayOfWeek)
            <tr>
                <td colspan="5" class="px-4 py-2 font-bold">
                    {{ $dayOfWeek['ru'] }} {{ $dayOfWeek['date'] }}
                    @if(\Carbon\Carbon::now()->format('d.m.Y') == $dayOfWeek['date']) (Сегодня) @endif
                </td>
            </tr>
                @if(in_array($dayOfWeek['en'], array_column($currentSchedule, 'day')))
                    @foreach($currentSchedule as $value)
                        @if(\Carbon\Carbon::parse($value['date'])->shortEnglishDayOfWeek == $dayOfWeek['en'])
                            @foreach($value['schedule']['data'] as $schedule)
                            <tr>
                                <td class="w-8 border py-2 text-center">{{ $schedule['lesson_number'] }}</td>
                                <td class="w-16 lg:w-24 border py-2 text-center">{{ $schedule['time'] }}</td>
                                <td class="w-auto border px-2 py-2">{{ $schedule['discipline'] }} ({{ $schedule['lesson_type'] }})</td>
                                <td class="w-auto border px-2 py-2">
                                    @foreach($schedule['data'] as $data)
                                        @role('Student')
                                            {{ $data['teacher'] }}
                                        @endrole
                                        @role('Teacher')
                                            {{ $data['group'] }}
                                        @endrole
                                    @endforeach
                                </td>
                                <td class="w-10 lg:w-32 border px-2 py-2">{{ $schedule['lesson_place'] }}</td>
                            </tr>
                            @endforeach
                        @endif
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="border px-4 py-2">
                            <span class="bg-orange-500 rounded px-2 py-1 text-xs text-white font-bold">Занятия отсутствуют</span>
                        </td>
                    </tr>
                @endif
            </tr>
            @endforeach
        </table>
        @else
        <div class="flex items-center bg-orange-500 border-t-4 border-orange-700 rounded shadow-xl p-4 text-white" role="alert">
            <i class="fa-solid fa-circle-exclamation fa-lg"></i>
            <div class="ml-3 text-base font-medium">
                На текущей неделе у Вас занятия не проводятся. Для отображения информации за предыдущие или будущие недели выберите соответствующие данные из выпадающих списков.
            </div>
        </div>
        @endif
    </div>
</div>