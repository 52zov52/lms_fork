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
            <div class="border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Дисциплина</th>
                            <th scope="col" class="w-40 px-2 py-2 text-center font-semibold">Тип работ</th>
                            <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Подгруппа</th>
                            <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Часы</th>
                            <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Группа</th>
                            <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Кол-во</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $levelEducationKey => $levelEducationValue)
                        @if(($levelEducationKey == 'spo' || $levelEducationKey == 'vo') && count($levelEducationValue['first_half_year']) != 0)
                            <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                <td colspan="6" class="w-auto px-2 py-1 font-semibold">
                                    {{ ($levelEducationKey == 'spo') ? __('Среднее профессиональное образование (СПО)') : __('Высшее образование (ВО)') }}
                                </td>
                            </tr>
                        @endif
                        @foreach($levelEducationValue['first_half_year'] as $disciplineKey => $disciplineValue)
                            @php
                                $totalRowsDiscipline = array_sum(array_map(fn($typeWorkValue) => array_sum(array_map('count', $typeWorkValue)), $disciplineValue));
                                $incrementDiscipline = 0;
                            @endphp
                            @foreach($disciplineValue as $typeWorkKey => $typeWorkValue)
                                @php
                                    $totalRowsTypeWork = array_sum(array_map('count', $typeWorkValue));
                                    $incrementTypeWork = 0;
                                @endphp
                                @foreach($typeWorkValue as $subgroupKey => $subgroupValue)
                                    @php
                                        $totalRowsSubgroup = count($subgroupValue);
                                        $incrementSubgroup = 0;
                                    @endphp
                                    @foreach($subgroupValue as $key => $value)
                                        <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                            @if($incrementDiscipline == 0)
                                                <td rowspan="{{ $totalRowsDiscipline }}" class="w-auto border-r border-slate-400/20 px-2 py-1">{{ $disciplineKey }}</td>
                                            @endif
                                            @if($incrementTypeWork == 0)
                                                <td rowspan="{{ $totalRowsTypeWork }}" class="w-40 border-r border-slate-400/20 text-center px-2 py-1">{{ $typeWorkKey }}</td>
                                            @endif
                                            @if($incrementSubgroup == 0)
                                                <td rowspan="{{ $totalRowsSubgroup }}" class="w-32 border-r border-slate-400/20 text-center px-2 py-1">{{ explode('|',$subgroupKey)[0] }}</td>
                                                <td rowspan="{{ $totalRowsSubgroup }}" class="w-20 border-r border-slate-400/20 text-center px-2 py-1">{{ explode('|',$subgroupKey)[1] }}</td>
                                            @endif
                                            <td class="w-20 border-r border-slate-400/20 text-center px-2 py-1">{{ $value['group_name'] }}</td>
                                            <td class="w-20 text-center px-2 py-1">{{ $value['student_count'] }}</td>
                                        </tr>
                                        @php
                                            $incrementDiscipline++;
                                            $incrementTypeWork++;
                                            $incrementSubgroup++;
                                        @endphp
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4" :class="{ 'active': activeTab === 1 }" x-cloak x-show.transition.in.opacity.duration.600="activeTab === 1">
            <div class="border rounded border-slate-400/20">
                <table class="w-full text-xs lg:text-sm">
                    <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                        <tr class="border-b border-slate-400/20">
                            <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Дисциплина</th>
                            <th scope="col" class="w-40 px-2 py-2 text-center font-semibold">Тип работ</th>
                            <th scope="col" class="w-32 px-2 py-2 text-center font-semibold">Подгруппа</th>
                            <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Часы</th>
                            <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Группа</th>
                            <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Кол-во</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $levelEducationKey => $levelEducationValue)
                        <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                            <td colspan="6" class="w-auto px-2 py-1 font-semibold">
                                @if($levelEducationKey == 'spo')
                                    {{ __('Среднее профессиональное образование (СПО)') }}
                                @else
                                    {{ __('Высшее образование (ВО)') }}
                                @endif
                            </td>
                        </tr>
                        @foreach($levelEducationValue['second_half_year'] as $disciplineKey => $disciplineValue)
                            @php
                                $totalRowsDiscipline = array_sum(array_map(fn($typeWorkValue) => array_sum(array_map('count', $typeWorkValue)), $disciplineValue));
                                $incrementDiscipline = 0;
                            @endphp
                            @foreach($disciplineValue as $typeWorkKey => $typeWorkValue)
                                @php
                                    $totalRowsTypeWork = array_sum(array_map('count', $typeWorkValue));
                                    $incrementTypeWork = 0;
                                @endphp
                                @foreach($typeWorkValue as $subgroupKey => $subgroupValue)
                                    @php
                                        $totalRowsSubgroup = count($subgroupValue);
                                        $incrementSubgroup = 0;
                                    @endphp
                                    @foreach($subgroupValue as $key => $value)
                                        <tr class="bg-white dark:bg-slate-800 border-b border-slate-400/20">
                                            @if($incrementDiscipline == 0)
                                                <td rowspan="{{ $totalRowsDiscipline }}" class="w-auto border-r border-slate-400/20 px-2 py-1">{{ $disciplineKey }}</td>
                                            @endif
                                            @if($incrementTypeWork == 0)
                                                <td rowspan="{{ $totalRowsTypeWork }}" class="w-40 border-r border-slate-400/20 text-center px-2 py-1">{{ $typeWorkKey }}</td>
                                            @endif
                                            @if($incrementSubgroup == 0)
                                                <td rowspan="{{ $totalRowsSubgroup }}" class="w-32 border-r border-slate-400/20 text-center px-2 py-1">{{ explode('|',$subgroupKey)[0] }}</td>
                                                <td rowspan="{{ $totalRowsSubgroup }}" class="w-20 border-r border-slate-400/20 text-center px-2 py-1">{{ explode('|',$subgroupKey)[1] }}</td>
                                            @endif
                                            <td class="w-20 border-r border-slate-400/20 text-center px-2 py-1">{{ $value['group_name'] }}</td>
                                            <td class="w-20 text-center px-2 py-1">{{ $value['student_count'] }}</td>
                                        </tr>
                                        @php
                                            $incrementDiscipline++;
                                            $incrementTypeWork++;
                                            $incrementSubgroup++;
                                        @endphp
                                    @endforeach
                                @endforeach
                            @endforeach
                        @endforeach
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