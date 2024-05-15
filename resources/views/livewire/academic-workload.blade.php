<div class="flex flex-col space-y-2">

    <h4 class="text-base lg:text-base font-semibold">
        {{ $academicYear }} учебный год
    </h4>

    @if(!is_null($data) && count($data) != 0)
    <!-- Desktop -->
    <div x-data="{ activeTab: 0 }">
        <ul class="flex flex-row space-x-1" role="tablist">
            <li role="tab" tabindex="0">
                <a @click="activeTab = 0" class="hover:cursor-pointer">Первое полугодие</a>
            </li>
            <li role="tab" tabindex="1">
                <a @click="activeTab = 1" class="hover:cursor-pointer">Второе полугодие</a>
            </li>
        </ul>
        <div class="mt-4" :class="{ 'active': activeTab === 0 }" x-show.transition.in.opacity.duration.600="activeTab === 0">
            <div class="border rounded border-slate-400/20">
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
                        @foreach($data['first_half_year'] as $firstHalfYear)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto px-2 py-2">{{ $firstHalfYear['discipline_name'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $firstHalfYear['group_names'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $firstHalfYear['control_name'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $firstHalfYear['lecture_hours'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $firstHalfYear['practical_hours'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $firstHalfYear['laboratory_hours'] }}</td>
                            <td class="w-20 px-2 py-2 text-center">{{ $firstHalfYear['semester_numbers'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4" :class="{ 'active': activeTab === 1 }" x-show.transition.in.opacity.duration.600="activeTab === 1">
            <div class="border rounded border-slate-400/20">
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
                        @foreach($data['second_half_year'] as $secondHalfYear)
                        <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                            <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                            <td class="w-auto px-2 py-2">{{ $secondHalfYear['discipline_name'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $secondHalfYear['group_names'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $secondHalfYear['control_name'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $secondHalfYear['lecture_hours'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $secondHalfYear['practical_hours'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $secondHalfYear['laboratory_hours'] }}</td>
                            <td class="w-20 px-2 py-2 text-center">{{ $secondHalfYear['semester_numbers'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Mobile -->
    @else
    <x-alert type="info" title="Информация" message="Учебная нагрузка отсутствует" />
    @endif
</div>