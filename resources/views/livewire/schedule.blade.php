<div class="flex flex-col space-y-2">
    @if(session()->has('message-success'))
    <div class="flex items-center bg-green-500 border-t-4 border-green-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            {{ session('message-success') }}
        </div>
    </div>
    @endif

    <div class="flex items-center bg-sky-500 border-t-4 border-sky-700 rounded shadow-xl p-4 text-white" role="alert">
        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
        <div class="ml-3 text-base font-medium">
            Дата и время обновления расписания: {{ $lastUpdateDate }}
        </div>
    </div>
    
    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
        <select wire:model="weekRange" wire:change="changeValue" name="weekRange" id="week-range-id" class="w-full md:w-auto border border-gray-300 rounded text-sm md:text-base focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите неделю</option>
            @foreach($weeksRange as $weekRange)
            <option value="{{ $weekRange['date'] }}">{{ $weekRange['date'] }}</option>
            @endforeach
        </select>
        @role('Admin')
        <button wire:click="uploadSchedule" class="w-full md:w-auto bg-blue-500 hover:bg-blue-600 rounded px-2 py-2 text-white">Обновить расписание</button>
        @endrole
    </div>

    <div class="bg-white rounded shadow-xl p-4">
        @if(!is_null($currentSchedule) && count($currentSchedule) != 0)
        <table class="w-full bg-white border shadow-md text-xs md:text-sm lg:text-base">
            @foreach($daysOfWeek as $dayOfWeek)
            <tr>
                <td colspan="5" class="px-4 py-2 font-bold">{{ $dayOfWeek['ru'] }}</td>
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
                Информация отсутсвует. Для отображения информации выберите данные из списков.
            </div>
        </div>
        @endif
    </div>
</div>