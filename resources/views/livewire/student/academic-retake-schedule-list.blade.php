<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        @if(!is_null($academicRetakeSchedules) && count($academicRetakeSchedules) != 0)
        <div class="hidden lg:block border rounded border-slate-400/20">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 font-semibold">№ пп</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Преподаватель</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Дата</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Время</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Аудитория</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Учебный год</th>
                        <th scope="col" class="w-auto px-2 py-2 font-semibold">Полугодие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($academicRetakeSchedules as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->fio }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ \Carbon\Carbon::parse($value->retake_date)->format('d.m.Y') }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->retake_time }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->room }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->academic_year }}</td>
                        <td class="w-auto px-2 py-2 text-center">{{ $value->half_year }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="lg:hidden flex flex-col space-y-2">
            @foreach($academicRetakeSchedules as $value)
            <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Преподаватель:</span>
                    <span>{{ $value->fio }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дата пересдачи:</span>
                    <span>{{ \Carbon\Carbon::parse($value->retake_date)->format('d.m.Y') }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Период пересдачи (время):</span>
                    <span>{{ $value->retake_time }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Аудитория:</span>
                    <span>{{ $value->room }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Учебный год:</span>
                    <span>{{ $value->academic_year }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Полугодие:</span>
                    <span>{{ $value->half_year }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-2">
            {{ $academicRetakeSchedules->links() }}
        </div>
        @else
        <x-alert type="info" title="Информация" message="График пересдач академических задолженностей на текущий учебный год отсутствует" />
        @endif
    </div>
</div>
</div>