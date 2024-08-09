<div class="flex flex-col space-y-2">

    <h4 class="text-base lg:text-base font-semibold">
        {{ $academicYear }} учебный год
    </h4>

    @if(!is_null($data) && count($data) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Учебный год</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Группа</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Кол-во студентов</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Полугодие</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Дисциплина</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Вид работы</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-16 px-2 py-2 text-center">{{ $value->year }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->group_name }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->student_count }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->semester }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->discipline }}</td>
                    <td class="w-16 px-2 py-2 text-center">{{ $value->type_work }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <x-alert type="info" title="Информация" message="Учебная нагрузка отсутствует" />
    @endif
</div>