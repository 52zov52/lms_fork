<div class="flex flex-col space-y-2">

    <x-toastr type="{{ session('type') }}" message="{{ session('message') }}" />

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
        <select wire:model="weekRange" wire:change="changeWeekRange" name="weekRange" id="week-range-id" class="w-full md:w-auto bg-white dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            <option value="0">Выберите неделю</option>
            @foreach($weeksRange as $weekRange)
                <option value="{{ $weekRange['date'] }}">{{ $weekRange['date'] }}</option>
            @endforeach
        </select>
        </div>
        <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
            @can('update-schedule')
            <button type="button" onclick="showToast('uploadSchedule')" class="bg-white hover:bg-blue-400 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-blue-400 px-2 py-2 text-xs text-left text-blue-400 hover:text-white font-bold">
                <i class="fa-solid fa-rotate"></i>
                Обновить расписание
            </button>
            @endcan
        </div>
    </div>

    @if(!is_null($currentSchedule) && count($currentSchedule) != 0)
        <div class="border rounded border-slate-400/20">
            <table class="w-full text-xs lg:text-sm">
                @foreach($daysOfWeek as $dayOfWeek)
                    <tr class="{{ (\Carbon\Carbon::now()->format('d.m.Y') == $dayOfWeek['date']) ? 'border-l-4 border-l-green-500' : ''}}">
                        <td colspan="5" class="bg-slate-200 dark:bg-slate-700 px-4 py-2 font-bold uppercase">
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

@push('scripts')
<script>
    async function showToast(methodName) {
        await @this.callMethod(methodName);;

        const toast = document.getElementById("toast");
        toast.className = toast.className.replace(" translate-x-full opacity-0", " translate-x-0 opacity-100");

        setTimeout(function() {
            toast.className = toast.className.replace(" translate-x-0 opacity-100", " translate-x-full opacity-0");
        }, 3500);
    }
</script>
@endpush