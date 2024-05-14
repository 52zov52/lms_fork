<div class="flex flex-col space-y-2">
    @if(!is_null($data) && count($data) != 0)
    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">Наименование дисциплины (модуля)</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Группа</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Форма контроля</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Кол-во часов лекций</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Кол-во часов практик</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Кол-во часов лабораторных</th>
                    <th scope="col" class="w-20 px-2 py-2 font-semibold">Семестр</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $key => $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $value['discipline_name'] }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value['group_names'] }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value['control_name'] }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value['lecture_hours'] }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value['practical_hours'] }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value['laboratory_hours'] }}</td>
                    <td class="w-20 px-2 py-2 text-center">{{ $value['semester_numbers'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Mobile -->
    @else
    <x-alert type="info" title="Информация" message="Учебная нагрузка отсутствует" />
    @endif
</div>