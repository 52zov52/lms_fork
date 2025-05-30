<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <h4 class="text-base lg:text-base font-semibold">
            {{ $academicYear }} учебный год
        </h4>

        @if(!is_null($data) && count($data) != 0)
        <div x-data="{ activeTab: 0 }">
            <ul class="flex flex-row space-x-1" role="tablist">
                <li :class="{ 'bg-slate-200 dark:bg-slate-700 border-t border-l border-r rounded-t': activeTab == 0, 'border rounded-t': activeTab != 0 }" class="border-slate-400/20 p-2" role="tab" tabindex="0">
                    <a @click="activeTab = 0" class="hover:cursor-pointer">Первое полугодие</a>
                </li>
                <li :class="{ 'bg-slate-200 dark:bg-slate-700 border-t border-l border-r rounded-t': activeTab == 1, 'border rounded-t': activeTab != 1 }" class="border-slate-400/20 p-2" role="tab" tabindex="1">
                    <a @click="activeTab = 1" class="hover:cursor-pointer">Второе полугодие</a>
                </li>
            </ul>

            <div class="mt-2 flex space-x-2 text-sm">
                <svg class="w-5 h-5 fill-current text-gray-600 dark:text-white" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0)">
                        <path d="M17.826 1.739H12.61a.435.435 0 0 0 0 .87h5.217a.435.435 0 0 0 0-.87z"></path>
                        <path d="M18.134 1.866L16.395.127a.435.435 0 0 0-.615.615l1.432 1.431-1.432 1.433a.435.435 0 0 0 .615.614l1.74-1.739a.435.435 0 0 0 0-.615zM6.522 1.739H1.304a.435.435 0 0 0 0 .87h5.218a.435.435 0 0 0 0-.87z"></path>
                        <path d="M1.919 2.173L3.35.742a.435.435 0 0 0-.615-.615L.996 1.867a.435.435 0 0 0 0 .614l1.74 1.74a.437.437 0 0 0 .614-.001.435.435 0 0 0 0-.615L1.92 2.173zM17.392 8.695c-.358 0-.69.109-.967.294a1.744 1.744 0 0 0-2.61-.87 1.743 1.743 0 0 0-1.641-1.163c-.316 0-.614.085-.87.234V4.347c0-.959-.78-1.739-1.739-1.739-.959 0-1.739.78-1.739 1.74v7.39l-1.511-1.132a2.28 2.28 0 0 0-2.965.21 1.305 1.305 0 0 0 0 1.845l6.193 6.193A3.89 3.89 0 0 0 12.31 20h2.037a4.788 4.788 0 0 0 4.783-4.783v-4.783c0-.959-.78-1.739-1.74-1.739zm.87 6.522a3.918 3.918 0 0 1-3.914 3.913h-2.037a3.024 3.024 0 0 1-2.153-.89l-6.193-6.194a.435.435 0 0 1 0-.615 1.403 1.403 0 0 1 1.829-.13L8 12.957a.436.436 0 0 0 .696-.348v-8.26a.87.87 0 0 1 1.739 0v6.521a.435.435 0 0 0 .87 0V8.695a.87.87 0 0 1 1.739 0v2.174a.435.435 0 0 0 .87 0V9.565a.87.87 0 0 1 1.738 0v1.304a.435.435 0 0 0 .87 0v-.434a.87.87 0 0 1 1.74 0v4.782z"></path>
                    </g>
                    <defs>
                        <clipPath id="clip0">
                            <path fill="#fff" d="M0 0h20v20H0z"></path>
                        </clipPath>
                    </defs>
                </svg>
                <span>Используйте для просмотра всей таблицы</span>
            </div>

            <div class="mt-2 viewport-width-wrapper border rounded border-slate-400/20 overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300" :class="{ 'active': activeTab === 0 }" x-cloak x-show.transition.in.opacity.duration.600="activeTab === 0">
                <div class="border rounded border-slate-400/20">
                    <table class="w-full text-xs lg:text-sm">
                        <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                            <tr class="border-b border-slate-400/20">
                                <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Дисциплина</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Семестр / Заезд</th>
                                <th scope="col" class="w-40 px-2 py-2 text-left font-semibold">Тип работ</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Часы</th>
                                <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Подгруппа</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Группа</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Кол-во</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $levelEducationKey => $levelEducationValue)
                            @if(($levelEducationKey == 'spo' || $levelEducationKey == 'vo') && count($levelEducationValue['first_half_year']['disciplineData']) != 0)
                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                    <td colspan="6" class="w-auto px-2 py-1 font-semibold">
                                        {{ ($levelEducationKey == 'spo') ? __('Среднее профессиональное образование (СПО)') : __('Высшее образование (ВО)') }}
                                    </td>
                                </tr>
                            @endif
                            @foreach($levelEducationValue['first_half_year']['disciplineData'] as $disciplineKey => $disciplineValue)
                                @php
                                    $totalRowsDiscipline = array_sum(array_map(
                                        fn($semesterValue) => array_sum(array_map(
                                            fn($typeWorkValue) => array_sum(array_map(
                                                fn($subgroupValue) => count($subgroupValue['groupData']),
                                                $typeWorkValue
                                            )), $semesterValue['typeWorkData']
                                        )), $disciplineValue['semesterArrivalData']
                                    ));
                                    $incrementDiscipline = 0;
                                @endphp
                                @foreach($disciplineValue['semesterArrivalData'] as $semesterKey => $semesterValue)
                                    @php
                                        $totalRowsSemester = array_sum(array_map(
                                            fn($typeWorkValue) => array_sum(array_map(
                                                fn($subgroupValue) => count($subgroupValue['groupData']),
                                                $typeWorkValue
                                            )), $semesterValue['typeWorkData']
                                        ));
                                        $incrementSemester = 0;
                                    @endphp
                                    @foreach($semesterValue['typeWorkData'] as $typeWorkKey => $typeWorkValue)
                                        @php
                                            $totalRowsTypeWork = array_sum(array_map(
                                                fn($subgroupValue) => count($subgroupValue['groupData']),
                                                $typeWorkValue
                                            ));
                                            $incrementTypeWork = 0;
                                        @endphp
                                        @foreach($typeWorkValue as $subgroupKey => $subgroupValue)
                                            @php
                                                $totalRowsSubgroup = count($subgroupValue['groupData']);
                                                $incrementSubgroup = 0;
                                            @endphp
                                            @foreach($subgroupValue['groupData'] as $key => $value)
                                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                                    @if($incrementDiscipline == 0)
                                                        <td rowspan="{{ $totalRowsDiscipline }}" class="w-auto border-r border-slate-400/20 px-2 text-left">{{ $disciplineKey }}</td>
                                                    @endif
                                                    @if($incrementSemester == 0)
                                                        <td rowspan="{{ $totalRowsSemester }}" class="w-20 border-r border-slate-400/20 px-2 text-center">{{ $semesterKey }}</td>
                                                    @endif
                                                    @if($incrementTypeWork == 0)
                                                        <td rowspan="{{ $totalRowsTypeWork }}" class="w-40 border-r border-slate-400/20 px-2 text-left">{{ $typeWorkKey }}</td>
                                                    @endif
                                                    @if($incrementSubgroup == 0)
                                                        <td rowspan="{{ $totalRowsSubgroup }}" class="w-20 border-r border-slate-400/20 px-2 text-center">{{ $subgroupValue['subgroupHours'] }}</td>
                                                        <td rowspan="{{ $totalRowsSubgroup }}" class="w-32 border-r border-slate-400/20 px-2 text-center">{{ $subgroupKey }}</td>
                                                    @endif
                                                    <td class="w-20 border-r border-slate-400/20 px-2 text-center">{{ $value['group_name'] }}</td>
                                                    <td class="w-20 px-2 text-center">{{ $value['student_count'] }}</td>
                                                </tr>
                                                @php
                                                    $incrementDiscipline++;
                                                    $incrementSemester++;
                                                    $incrementTypeWork++;
                                                    $incrementSubgroup++;
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                    <td class="w-auto px-2 py-2 text-left font-semibold"></td>
                                    <td class="w-20 px-2 py-2 text-left font-semibold"></td>
                                    <td class="w-40 px-2 py-2 text-left font-semibold"></td>
                                    <td class="w-20 border border-slate-400/20 px-2 py-2 text-center font-semibold">{{ $disciplineValue['disciplineHours'] }}</td>
                                    <td class="w-32 px-2 py-2 text-center font-semibold"></td>
                                    <td class="w-20 px-2 py-2 text-center font-semibold"></td>
                                    <td class="w-20 px-2 py-2 text-center font-semibold"></td>
                                </tr>
                            @endforeach
                            @if($levelEducationValue['first_half_year']['totalHours'] > 0)
                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                    <td colspan="6" class="w-auto px-2 py-1 font-semibold">
                                        {{ __('ВСЕГО:') }} {{ $levelEducationValue['first_half_year']['totalHours'] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-2 viewport-width-wrapper border rounded border-slate-400/20 overflow-auto scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-300" :class="{ 'active': activeTab === 1 }" x-cloak x-show.transition.in.opacity.duration.600="activeTab === 1">
                <div class="border rounded border-slate-400/20">
                    <table class="w-full text-xs lg:text-sm">
                        <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                            <tr class="border-b border-slate-400/20">
                                <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Дисциплина</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Семестр / Заезд</th>
                                <th scope="col" class="w-40 px-2 py-2 text-left font-semibold">Тип работ</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Часы</th>
                                <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Подгруппа</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Группа</th>
                                <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Кол-во</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $levelEducationKey => $levelEducationValue)
                            @if(($levelEducationKey == 'spo' || $levelEducationKey == 'vo') && count($levelEducationValue['second_half_year']['disciplineData']) != 0)
                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                    <td colspan="6" class="w-auto px-2 py-1 font-semibold">
                                        {{ ($levelEducationKey == 'spo') ? __('Среднее профессиональное образование (СПО)') : __('Высшее образование (ВО)') }}
                                    </td>
                                </tr>
                            @endif
                            @foreach($levelEducationValue['second_half_year']['disciplineData'] as $disciplineKey => $disciplineValue)
                                @php
                                    $totalRowsDiscipline = array_sum(array_map(
                                        fn($semesterValue) => array_sum(array_map(
                                            fn($typeWorkValue) => array_sum(array_map(
                                                fn($subgroupValue) => count($subgroupValue['groupData']),
                                                $typeWorkValue
                                            )), $semesterValue['typeWorkData']
                                        )), $disciplineValue['semesterArrivalData']
                                    ));
                                    $incrementDiscipline = 0;
                                @endphp
                                @foreach($disciplineValue['semesterArrivalData'] as $semesterKey => $semesterValue)
                                    @php
                                        $totalRowsSemester = array_sum(array_map(
                                            fn($typeWorkValue) => array_sum(array_map(
                                                fn($subgroupValue) => count($subgroupValue['groupData']),
                                                $typeWorkValue
                                            )), $semesterValue['typeWorkData']
                                        ));
                                        $incrementSemester = 0;
                                    @endphp
                                    @foreach($semesterValue['typeWorkData'] as $typeWorkKey => $typeWorkValue)
                                        @php
                                            $totalRowsTypeWork = array_sum(array_map(
                                                fn($subgroupValue) => count($subgroupValue['groupData']),
                                                $typeWorkValue
                                            ));
                                            $incrementTypeWork = 0;
                                        @endphp
                                        @foreach($typeWorkValue as $subgroupKey => $subgroupValue)
                                            @php
                                                $totalRowsSubgroup = count($subgroupValue['groupData']);
                                                $incrementSubgroup = 0;
                                            @endphp
                                            @foreach($subgroupValue['groupData'] as $key => $value)
                                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                                    @if($incrementDiscipline == 0)
                                                        <td rowspan="{{ $totalRowsDiscipline }}" class="w-auto border-r border-slate-400/20 px-2 text-left">{{ $disciplineKey }}</td>
                                                    @endif
                                                    @if($incrementSemester == 0)
                                                        <td rowspan="{{ $totalRowsSemester }}" class="w-20 border-r border-slate-400/20 px-2 text-center">{{ $semesterKey }}</td>
                                                    @endif
                                                    @if($incrementTypeWork == 0)
                                                        <td rowspan="{{ $totalRowsTypeWork }}" class="w-40 border-r border-slate-400/20 px-2 text-left">{{ $typeWorkKey }}</td>
                                                    @endif
                                                    @if($incrementSubgroup == 0)
                                                        <td rowspan="{{ $totalRowsSubgroup }}" class="w-20 border-r border-slate-400/20 px-2 text-center">{{ $subgroupValue['subgroupHours'] }}</td>
                                                        <td rowspan="{{ $totalRowsSubgroup }}" class="w-32 border-r border-slate-400/20 px-2 text-center">{{ $subgroupKey }}</td>
                                                    @endif
                                                    <td class="w-20 border-r border-slate-400/20 px-2 text-center">{{ $value['group_name'] }}</td>
                                                    <td class="w-20 px-2 text-center">{{ $value['student_count'] }}</td>
                                                </tr>
                                                @php
                                                    $incrementDiscipline++;
                                                    $incrementSemester++;
                                                    $incrementTypeWork++;
                                                    $incrementSubgroup++;
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                @endforeach
                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                    <td class="w-auto px-2 py-2 text-left font-semibold"></td>
                                    <td class="w-20 px-2 py-2 text-left font-semibold"></td>
                                    <td class="w-40 px-2 py-2 text-left font-semibold"></td>
                                    <td class="w-20 border border-slate-400/20 px-2 py-2 text-center font-semibold">{{ $disciplineValue['disciplineHours'] }}</td>
                                    <td class="w-32 px-2 py-2 text-center font-semibold"></td>
                                    <td class="w-20 px-2 py-2 text-center font-semibold"></td>
                                    <td class="w-20 px-2 py-2 text-center font-semibold"></td>
                                </tr>
                            @endforeach
                            @if($levelEducationValue['second_half_year']['totalHours'] > 0)
                                <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                    <td colspan="6" class="w-auto px-2 py-1 font-semibold">
                                        {{ __('ВСЕГО:') }} {{ $levelEducationValue['second_half_year']['totalHours'] }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <x-alert type="info" title="Информация" message="Учебная нагрузка отсутствует" />
        @endif
    </div>
</div>
