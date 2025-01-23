<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <h4 class="text-base lg:text-base font-semibold">
            Группа {{ $group_name }}
        </h4>

        @if(!is_null($courseList) && count($courseList) != 0)
        <div class="border rounded border-slate-400/20">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                        <th scope="col" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-center font-semibold">Дисциплина</th>
                        <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Полугодие</th>
                        <th scope="col" class="w-24 px-2 py-2 text-left font-semibold">Семестр</th>
                        <th scope="col" class="sticky right-0 w-20 bg-slate-200 dark:bg-slate-700 px-2 py-2 text-right font-semibold">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courseList as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2">{{ $value->name }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $value->semester_number }}</td>
                        <td class="sticky right-0 w-20 flex flex-row space-x-1 bg-white dark:bg-slate-800 px-2 py-2 text-center">
                            <button wire:click="redirectToEJournal({{ $value->wsp_courses_in_semester_id }})" class="w-9 btn-course-list" data-tippy-content="Журнал группы">
                                <i class="fa-solid fa-book-open"></i>
                            </button>
                            <button wire:click="groupAttendanceReport({{ $value->wsp_courses_in_semester_id }})" class="w-9 btn-course-list" data-tippy-content="Отчет о посещаемости группы">
                                <i class="fa-solid fa-chart-column"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-alert type="info" title="Информация" message="Список учебных учебных дисциплин для группы {{ $group_name }} отсутствует" />
        @endif
    </div>
</div>