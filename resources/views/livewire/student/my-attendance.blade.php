<div class="flex flex-col space-y-2">

    @if(!is_null($eJournals) && count($eJournals) != 0)
    @foreach($eJournals as $semester => $data)
    <div x-data="{ isOpenSemester: false }">
        <div @click="isOpenSemester = !isOpenSemester" class="flex items-center justify-between bg-gray-200 dark:bg-slate-800 border border-slate-400/20 p-4 select-none">
            <p>Семестр {{ $semester }}</p>
            <span :class="isOpenSemester ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa-solid"></span>
        </div>
        <div x-show="isOpenSemester" class="border border-slate-400/20 p-4">
            @foreach($data as $discipline => $attendance)
            <div x-data="{ isOpenEJournal: false }">
                <div @click="isOpenEJournal = !isOpenEJournal" class="flex items-center justify-between bg-gray-200 dark:bg-slate-800 border border-slate-400/20 p-4 select-none">
                    <p>{{ $discipline }}</p>
                    <span :class="isOpenEJournal ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa-solid"></span>
                </div>
                <div x-show="isOpenEJournal" class="border border-slate-400/20 p-4">
                    <div class="border rounded border-slate-400/20">
                        <table class="w-full text-xs lg:text-sm">
                            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                                <tr class="border-b border-slate-400/20">
                                    <th scope="col" class="w-1/4 px-2 py-2 text-center font-semibold">Дата</th>
                                    <th scope="col" class="w-1/4 px-2 py-2 text-center font-semibold">Время</th>
                                    <th scope="col" class="w-1/4 px-2 py-2 text-center font-semibold">Тип</th>
                                    <th scope="col" class="w-1/4 px-2 py-2 text-center font-semibold">Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($attendance as $key => $value)
                                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                                    <td class="w-1/4 px-2 py-2 text-center">{{ $value['lesson_date'] }}</td>
                                    <td class="w-1/4 px-2 py-2 text-center">{{ $value['lesson_time'] }}</td>
                                    <td class="w-1/4 px-2 py-2 text-center">
                                        @switch($value['lesson_type'])
                                            @case(1)
                                                <span>Лекция</span>
                                                @break
                                            @case(2)
                                                <span>Лабораторная</span>
                                                @break
                                            @case(3)
                                                <span>Практическая</span>
                                                @break
                                            @case(6)
                                                <span>Зачет</span>
                                                @break
                                            @case(7)
                                                <span>Диф. зачет</span>
                                                @break
                                            @case(7)
                                                <span>Экзамен</span>
                                                @break
                                            @default
                                                <span>-</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="w-1/4 px-2 py-2 text-center">
                                        @switch($value['status'])
                                            @case(1)
                                                <span>Присутствовал</span>
                                                @break
                                            @case(2)
                                                <span>Отсутсвовал</span>
                                                @break
                                            @default
                                                <span>-</span>
                                                @break
                                        @endswitch
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
    @else
    <x-alert type="info" title="Информация" message="Информация о вашей посещаемости учебных занятий отсутствует" />
    @endif
</div>