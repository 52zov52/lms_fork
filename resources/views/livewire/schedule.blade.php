<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:justify-between">
            <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
                @role('Teacher')
                <select wire:model="teacher" wire:change="changeTeacher" name="teachers" id="teachers-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="0">Выберите преподавателя</option>
                    @foreach($teachers as $value)
                    <option value="{{ $value->teacher }}">{{ $value->teacher }}</option>
                    @endforeach
                </select>
                @endrole
                <select wire:model="weekRange" wire:change="changeWeek" name="weekRange" id="week-range-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                    <option value="0">Выберите неделю</option>
                    @foreach($weeksRange as $weekRange)
                    <option value="{{ $weekRange['date'] }}">{{ $weekRange['date'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
                <button type="button" wire:click="exportScheduleToIcs" class="w-auto md:w-48 btn-download">
                    <span wire:loading.remove wire:target="exportScheduleToIcs">
                        <i class="fa-solid fa-download"></i>
                        Экспорт расписания в iCal
                    </span>
                    <span wire:loading wire:target="exportScheduleToIcs">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
                @can('update-schedule')
                <button type="button" wire:click="uploadSchedule" class="w-auto md:w-44 btn-blue">
                    <span wire:loading.remove wire:target="uploadSchedule">
                        <i class="fa-solid fa-rotate"></i>
                        Обновить расписание
                    </span>
                    <span wire:loading wire:target="uploadSchedule">
                        <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                    </span>
                </button>
                @endcan
            </div>
        </div>

        <div wire:loading.remove wire:target="changeWeek, changeTeacher">
            @if(!is_null($currentSchedule) && count($currentSchedule) != 0)
            <div class="border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    @foreach($daysOfWeek as $dayOfWeek)
                    <tr class="{{ (\Carbon\Carbon::now()->format('d.m.Y') == $dayOfWeek['date']) ? 'border-l-4 border-l-green-500' : ''}}">
                        <td colspan="6" class="bg-slate-200 dark:bg-slate-700 px-4 py-2 font-bold uppercase">
                            {{ $dayOfWeek['ru'] }} {{ $dayOfWeek['date'] }}
                            @if(\Carbon\Carbon::now()->format('d.m.Y') == $dayOfWeek['date']) (Сегодня) @endif
                        </td>
                    </tr>
                    @if(in_array($dayOfWeek['en'], array_column($currentSchedule, 'day')))
                    @foreach($currentSchedule as $value)
                    @if(\Carbon\Carbon::parse($value['date'])->shortEnglishDayOfWeek == $dayOfWeek['en'])
                    @foreach($value['schedule']['data'] as $schedule)
                    <tr class="border-b border-slate-400/20 {{ (\Carbon\Carbon::now()->format('d.m.Y') == $dayOfWeek['date']) ? 'border-l-4 border-l-green-500' : ''}}">
                        <td class="w-8 py-2 text-center">{{ $schedule['lesson_number'] }}</td>
                        <td class="w-16 lg:w-24 py-2 text-center">{{ $schedule['time'] }}</td>
                        <td class="w-auto px-2 py-2">{{ $schedule['discipline'] }} ({{ $schedule['lesson_type'] }})</td>
                        <td class="w-auto px-2 py-2">
                            @foreach($schedule['data'] as $data)
                            @role('Student')
                            {{ $data['teacher'] }}
                            @endrole
                            @role('Teacher')
                            {{ $data['group'] }}
                            @endrole
                            @endforeach
                        </td>
                        <td class="w-10 lg:w-32 px-2 py-2">{{ $schedule['lesson_place'] }}</td>
                        <td class="w-auto lg:w-32 px-2 py-2 text-center">
                            <button wire:click="getLinkToVideoConference({{ $schedule['id'] }})" data-tippy-content="Видеоконференция">
                                <i class="fa-solid fa-link"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                    @endforeach
                    @else
                    <tr class="border-b dark:border-b-slate-700 {{ (\Carbon\Carbon::now()->format('d.m.Y') == $dayOfWeek['date']) ? 'border-l-4 border-l-green-500' : ''}}">
                        <td colspan="5" class="px-4 py-2">
                            <span class="bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 px-2 py-1 text-xs text-orange-400 font-bold">Занятия отсутствуют</span>
                        </td>
                    </tr>
                    @endif
                    </tr>
                    @endforeach
                </table>
            </div>
            @else
            <x-alert type="info" title="Информация" message="На текущей неделе у Вас занятия не проводятся. Для отображения информации за предыдущие или будущие недели выберите соответствующие данные из выпадающих списков." />
            @endif
        </div>
        <!-- Preloader -->
        <div class="h-full" wire:loading wire:target="changeWeek, changeTeacher">
            <div class="h-full flex space-x-2 items-center justify-center text-sm md:text-lg">
                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                <span>Пожалуйста, подождите, идет загрузка данных</span>
            </div>
        </div>
    </div>
</div>