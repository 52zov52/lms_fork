<div class="flex flex-col space-y-2">

    @if(!is_null($myGrades) && count($myGrades) != 0)
        @foreach($myGrades as $semester => $data)
        <div x-data="{ open: false }">
            <div @click="open = !open" class="flex items-center justify-between bg-gray-200 border p-4 select-none">
                <p>Семестр {{ $semester }}</p>
                <span :class="open ? 'fa-chevron-down' : 'fa-chevron-up'" class="fa-solid"></span>
            </div>
            <div x-show.transition.in.duration.800ms="open" class="border p-4">
                <div class="border rounded border-slate-400/20">
                    <table class="w-full text-xs lg:text-sm">
                        <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                            <tr class="border-b border-slate-400/20">
                                <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Дисциплина</th>
                                <th scope="col" class="w-auto px-2 py-2 text-center font-semibold">Вид контроля</th>
                                <th scope="col" class="w-auto px-2 py-2 text-center font-semibold">Результат</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $value)
                            <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                                <td class="w-auto px-2 py-2 text-left">{{ $value['discipline_name'] }}</td>
                                <td class="w-auto px-2 py-2 text-center ">{{ $value['control_name'] }}</td>
                                <td class="w-auto px-2 py-2 text-center ">
                                    @if(is_null($value['grade']))
                                        Отсутсвует
                                    @else
                                        @if($value['control_name'] == 'Зачет')
                                            @if($value['grade'] == 1)
                                                Зачтено
                                            @else
                                                Не зачтено
                                            @endif
                                        @else
                                            {{ $value['grade'] }}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <x-alert type="info" title="Информация" message="Результаты промежуточной аттестации отсутствует" />
    @endif
</div>