<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <h4 class="text-base lg:text-base font-semibold">
            {{ $academicYear }} учебный год
        </h4>

        @if(!is_null($listOfGroups) && count($listOfGroups) != 0)
        <div class="border rounded border-slate-400/20">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                        <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Уровень образования</th>
                        <th scope="col" class="w-40 px-2 py-2 text-center font-semibold">Форма обучения</th>
                        <th scope="col" class="w-20 px-2 py-2 text-center font-semibold">Курс</th>
                        <th scope="col" class="sticky right-[80px] w-auto bg-slate-200 dark:bg-slate-700 px-2 py-2 text-left font-semibold">Группа</th>
                        <th scope="col" class="sticky right-0 w-20 bg-slate-200 dark:bg-slate-700 px-2 py-2 text-right font-semibold">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listOfGroups as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-24 px-2 py-2 text-center">
                            @if($value->level_education_id == 6 || $value->level_education_id == 7)
                                <span>ВО</span>
                            @elseif($value->level_education_id == 2)
                                <span>СПО</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td class="w-40 px-2 py-2 text-center">
                            @if($value->teaching_form_id == 1)
                                <span>Очная</span>
                            @elseif($value->teaching_form_id == 2)
                                <span>Заочная</span>
                            @elseif($value->teaching_form_id == 3)
                                <span>Заочная форма (ускоренное обучение)</span>
                            @elseif($value->teaching_form_id == 6)
                                <span>Очно-заочная</span>
                            @elseif($value->teaching_form_id == 7)
                                <span>Очная (ускоренное обучение)</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        <td class="w-20 px-2 py-2 text-center">{{ $value->course }}</td>
                        <td class="sticky right-[80px] w-auto bg-white dark:bg-slate-800 px-2 py-2">{{ $value->group_name }}</td>
                        <td class="sticky right-0 w-20 bg-white dark:bg-slate-800 px-2 py-2 text-center">
                            <button wire:click="redirectToCourseList({{ $value->id }})" class="w-9 btn-course-list" data-tippy-content="Список дисциплин группы">
                                <i class="fa-solid fa-rectangle-list"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-alert type="info" title="Информация" message="Список учебных групп на текущий учебный год отсутствует" />
        @endif
    </div>
</div>