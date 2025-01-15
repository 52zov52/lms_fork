<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        @if(!is_null($schedulesEvents) && count($schedulesEvents) != 0)
        <div x-data="{ open: false }">
            <div @click="open = !open" class="flex items-center justify-between bg-gray-200 dark:bg-slate-800 border border-slate-400/20 p-4 select-none">
                <p>Расписание занятий на период с {{ \Carbon\Carbon::now()->format('d.m.Y') }} по {{ \Carbon\Carbon::now()->addDays(3)->format('d.m.Y') }}</p>
                <span :class="open ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa-solid"></span>
            </div>
            <div x-show="open" class="border border-slate-400/20 p-4">
                <table class="w-full text-xs md:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-24 px-2 py-2 font-semibold">Вид занятий</th>
                            <th scope="col" class="w-auto px-2 py-2 font-semibold">Дисциплина</th>
                            <th scope="col" class="w-16 px-2 py-2 font-semibold">Дата</th>
                            <th scope="col" class="w-24 px-2 py-2 font-semibold">Часы занятий</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($schedulesEvents as $schedules)
                        <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                            <td class="w-24 px-2 py-2 text-center">{{ $schedules->lesson_type }}</td>
                            <td class="w-auto px-2 py-2">{{ $schedules->discipline }}</td>
                            <td class="w-16 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($schedules->date)->format('d.m.Y') }}</td>
                            <td class="w-24 px-2 py-2 text-center">{{ $schedules->time }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-slate-800">
                            <td colspan="4" class="w-24 px-2 py-2">Аудитория: {{ $schedules->lesson_place }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                            @role('Student')
                            <td colspan="4" class="w-40 px-2 py-2">Преподаватель: {{ $schedules->teacher }}</td>
                            @endrole
                            @role('Teacher')
                            <td colspan="4" class="w-40 px-2 py-2">Учебная группа: {{ $schedules->group }}</td>
                            @endrole
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        @if(!is_null($rectorateDecisionEvents) && count($rectorateDecisionEvents) != 0)
        <div x-data="{ open: false }">
            <div @click="open = !open" class="flex items-center justify-between bg-gray-200 dark:bg-slate-800 border border-slate-400/20 p-4 select-none">
                <p>Решения ректората на период с {{ \Carbon\Carbon::now()->format('d.m.Y') }} по {{ \Carbon\Carbon::now()->addDays(3)->format('d.m.Y') }}</p>
                <span :class="open ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa-solid"></span>
            </div>
            <div x-show="open" class="border border-slate-400/20 p-4">
                <table class="w-full text-xs md:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-auto px-2 py-2 font-semibold">Задача</th>
                            <th scope="col" class="w-16 px-2 py-2 font-semibold">Срок исполнения до</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rectorateDecisionEvents as $rectorateDecision)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-auto px-2 py-2">{{ $rectorateDecision->task }}</td>
                            <td class="w-16 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($rectorateDecision->due_date)->format('d.m.Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        @if(!is_null($upbringingEvents) && count($upbringingEvents) != 0)
        <div x-data="{ open: false }">
            <div @click="open = !open" class="flex items-center justify-between bg-gray-200 dark:bg-slate-800 border border-slate-400/20 p-4 select-none">
                <p>Воспитательная работа на период с {{ \Carbon\Carbon::now()->format('d.m.Y') }} по {{ \Carbon\Carbon::now()->addDays(3)->format('d.m.Y') }}</p>
                <span :class="open ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa-solid"></span>
            </div>
            <div x-show="open" class="border border-slate-400/20 p-4">
                <table class="w-full text-xs md:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-auto px-2 py-2 font-semibold">Задача</th>
                            <th scope="col" class="w-auto px-2 py-2 font-semibold">Описание</th>
                            <th scope="col" class="w-16 px-2 py-2 font-semibold">Срок исполнения до</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upbringingEvents as $upbringingEvent)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-auto px-2 py-2">{{ $upbringingEvent->name }}</td>
                            <td class="w-auto px-2 py-2">{{ $upbringingEvent->description }}</td>
                            <td class="w-16 px-2 py-2 text-center">{{ \Carbon\Carbon::parse($upbringingEvent->date)->format('d.m.Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>
</div>