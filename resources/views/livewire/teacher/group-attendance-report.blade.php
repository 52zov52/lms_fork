<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <h4 class="text-base lg:text-base font-semibold">
            Группа: {{ $group_name }}
        </h4>
        <h4 class="text-base lg:text-base font-semibold">
            Дисциплина: {{ $discipline_name }}
        </h4>
        <h4 class="text-base lg:text-base font-semibold">
            Учебный год: {{ $academicYear }} ({{ $halfYear }}-е полугодие)
        </h4>

        @if(count($eJournal) != 0)
        <div class="w-e-journal h-e-journal border rounded border-slate-400/20 overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                        <th scope="col" class="sticky left-0 w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-left font-semibold">ФИО</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">Кол-во прошедших занятий</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">Кол-во пропусков</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">% пропусков</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">Кол-во посещений</th>
                        <th scope="col" class="w-24 px-2 py-2 font-semibold">% посещений</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eJournal as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="sticky left-0 w-auto bg-white dark:bg-slate-800 px-2 py-2">{{ $value->fio }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $value->total }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $value->was_absent }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ round(($value->was_absent / $value->total) * 100, 2) }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $value->was_present }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ round(($value->was_present / $value->total) * 100, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-alert type="info" title="Информация" message="Информация о посещаемости для группы {{ $group_name }} отсутствует" />
        @endif
    </div>
</div>