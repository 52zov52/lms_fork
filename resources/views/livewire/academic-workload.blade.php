<div class="flex flex-col space-y-2">

    <h4 class="text-base lg:text-base font-semibold">
        {{ $academicYear }} учебный год
    </h4>

    @if(!is_null($data) && count($data) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full border">
            @foreach($data as $disciplineKey => $disciplineValue)
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
                            <tr class="border border-slate-400/20">
                                @if($incrementDiscipline == 0)
                                    <td rowspan="{{ $totalRowsDiscipline }}" class="border border-slate-400/20">{{ $disciplineKey }}</td>
                                @endif
                                @if($incrementTypeWork == 0)
                                    <td rowspan="{{ $totalRowsTypeWork }}" class="border border-slate-400/20">{{ $typeWorkKey }}</td>
                                @endif
                                @if($incrementSubgroup == 0)
                                    <td rowspan="{{ $totalRowsSubgroup }}" class="border border-slate-400/20">{{ $subgroupKey }}</td>
                                @endif
                                <td class="border border-slate-400/20">{{ $value['group_name'] }}</td>
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
        </table>
    </div>
    @else
    <x-alert type="info" title="Информация" message="Учебная нагрузка отсутствует" />
    @endif
</div>