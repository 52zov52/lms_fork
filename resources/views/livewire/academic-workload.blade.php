<div class="flex flex-col space-y-2">

    <h4 class="text-base lg:text-base font-semibold">
        {{ $academicYear }} учебный год
    </h4>

    @if(!is_null($data) && count($data) != 0)
    <div x-data="{ activeTab: 0 }">
        <ul class="flex flex-row space-x-1" role="tablist">
            <li :class="{ 'border-t border-l border-r rounded-t': activeTab == 0, 'border rounded-t': activeTab != 0 }" class="border-slate-400/20 p-2" role="tab" tabindex="0">
                <a @click="activeTab = 0" class="hover:cursor-pointer">Первое полугодие</a>
            </li>
            <li :class="{ 'border-t border-l border-r rounded-t': activeTab == 1, 'border rounded-t': activeTab != 1 }" class="border-slate-400/20 p-2" role="tab" tabindex="1">
                <a @click="activeTab = 1" class="hover:cursor-pointer">Второе полугодие</a>
            </li>
        </ul>
        <div class="mt-4" :class="{ 'active': activeTab === 0 }" x-cloak x-show.transition.in.opacity.duration.600="activeTab === 0">
            <!-- Desktop -->
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Наименование дисциплины (модуля)</th>
                            <th scope="col" class="w-32 px-2 py-2 text-left font-semibold">Группа</th>
                            <th scope="col" class="w-40 px-2 py-2 text-left font-semibold">Форма контроля</th>
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
                            <td class="w-32 px-2 py-2">{{ $firstHalfYear['group_name'] }}</td>
                            <td class="w-40 px-2 py-2">{{ $firstHalfYear['control_name'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $firstHalfYear['lecture_hours'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">
                                @if($firstHalfYear['practical_hours'] != 0)
                                    {{ $firstHalfYear['practical_hours'] }}
                                @elseif($firstHalfYear['group_consultation_hours'] != 0)
                                    {{ $firstHalfYear['group_consultation_hours'] }}
                                @else
                                    0
                                @endif
                            </td>
                            <td class="w-32 px-2 py-2 text-center">{{ $firstHalfYear['laboratory_hours'] }}</td>
                            <td class="w-20 px-2 py-2 text-center">{{ $firstHalfYear['semester_number'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Mobile -->
            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($data['first_half_year'] as $firstHalfYear)
                <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                    <div class="flex flex-col">
                        <span class="font-semibold">Наименование дисциплины (модуля):</span>
                        <span>{{ $firstHalfYear['discipline_name'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Группа:</span>
                        <span>{{ $firstHalfYear['group_name'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Форма контроля:</span>
                        <span>{{ $firstHalfYear['control_name'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Кол-во часов лекций:</span>
                        <span>{{ $firstHalfYear['lecture_hours'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Кол-во часов практик:</span>
                        <span>
                            @if($firstHalfYear['practical_hours'] != 0)
                                {{ $firstHalfYear['practical_hours'] }}
                            @elseif($firstHalfYear['group_consultation_hours'] != 0)
                                {{ $firstHalfYear['group_consultation_hours'] }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Кол-во часов лабораторных:</span>
                        <span>{{ $firstHalfYear['laboratory_hours'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Семестр:</span>
                        <span>{{ $firstHalfYear['semester_number'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="mt-4" :class="{ 'active': activeTab === 1 }" x-cloak x-show.transition.in.opacity.duration.600="activeTab === 1">
            <!-- Desktop -->
            <div class="hidden lg:block border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Наименование дисциплины (модуля)</th>
                            <th scope="col" class="w-32 px-2 py-2 text-left font-semibold">Группа</th>
                            <th scope="col" class="w-40 px-2 py-2 text-left font-semibold">Форма контроля</th>
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
                            <td class="w-32 px-2 py-2">{{ $secondHalfYear['group_name'] }}</td>
                            <td class="w-40 px-2 py-2">{{ $secondHalfYear['control_name'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">{{ $secondHalfYear['lecture_hours'] }}</td>
                            <td class="w-32 px-2 py-2 text-center">
                                @if($secondHalfYear['practical_hours'] != 0)
                                    {{ $secondHalfYear['practical_hours'] }}
                                @elseif($secondHalfYear['group_consultation_hours'] != 0)
                                    {{ $secondHalfYear['group_consultation_hours'] }}
                                @else
                                    0
                                @endif
                            </td>
                            <td class="w-32 px-2 py-2 text-center">{{ $secondHalfYear['laboratory_hours'] }}</td>
                            <td class="w-20 px-2 py-2 text-center">{{ $secondHalfYear['semester_number'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Mobile -->
            <div class="lg:hidden flex flex-col space-y-2">
                @foreach($data['second_half_year'] as $secondHalfYear)
                <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                    <div class="flex flex-col">
                        <span class="font-semibold">Наименование дисциплины (модуля):</span>
                        <span>{{ $secondHalfYear['discipline_name'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Группа:</span>
                        <span>{{ $secondHalfYear['group_name'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Форма контроля:</span>
                        <span>{{ $secondHalfYear['control_name'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Кол-во часов лекций:</span>
                        <span>{{ $secondHalfYear['lecture_hours'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Кол-во часов практик:</span>
                        <span>
                            @if($secondHalfYear['practical_hours'] != 0)
                                {{ $secondHalfYear['practical_hours'] }}
                            @elseif($secondHalfYear['group_consultation_hours'] != 0)
                                {{ $secondHalfYear['group_consultation_hours'] }}
                            @else
                                0
                            @endif
                        </span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Кол-во часов лабораторных:</span>
                        <span>{{ $secondHalfYear['laboratory_hours'] }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold">Семестр:</span>
                        <span>{{ $secondHalfYear['semester_number'] }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <x-alert type="info" title="Информация" message="Учебная нагрузка отсутствует" />
    @endif
</div>