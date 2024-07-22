<div class="flex flex-col space-y-2">

    <div class="flex flex-col space-y-2">
        <p class="text-xs lg:text-sm">№ ведомости {{ $bulletin->bulletin_number }}</p>
        <p class="text-xs lg:text-sm">Группа {{ $bulletin->group_name }}</p>
        <p class="text-xs lg:text-sm">Дисциплина: {{ $bulletin->discipline_name }}</p>
        <p class="text-xs lg:text-sm">Семестр: {{ $bulletin->semester_number }}</p>
        <p class="text-xs lg:text-sm">Форма контроля: {{ $bulletin->control_name }}</p>
        <p class="text-xs lg:text-sm">Преподаватель: {{ $bulletin->fio }}</p>
    </div>

    <div class="hidden lg:block border rounded border-slate-400/20">
        @if($bulletin->type_bulletin_id == 4)
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th rowspan="2" scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th rowspan="2" scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">№ зачетки</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">1-я контрольная точка</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">2-я контрольная точка</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">3-я контрольная точка</th>
                    <th colspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">4-я контрольная точка</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">Текущий контроль</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">Оценка за ответ</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">Итоговая оценка</th>
                </tr>
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">% за тест</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Балл</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentGrades as $key => $student)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $student->fio }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $student->ticket_number }}</td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->first_checkpoint))
                        -
                        @else
                        {{ $student->first_checkpoint }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->second_checkpoint))
                        -
                        @else
                        {{ $student->second_checkpoint }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->third_checkpoint))
                        -
                        @else
                        {{ $student->third_checkpoint }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->test_percentage_achieved))
                        -
                        @else
                        {{ $student->test_percentage_achieved }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->fourth_checkpoint))
                        -
                        @else
                        {{ $student->fourth_checkpoint }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->current_progress))
                        -
                        @else
                        {{ $student->current_progress }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->answer_grade))
                        -
                        @else
                        {{ $student->answer_grade }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if($bulletin->control_name == 'Зачет')
                        {{ ($student->grade == 1) ? 'Зачтено' : 'Не зачтено' }}
                        @elseif(is_null($student->grade))
                        -
                        @else
                        {{ $student->grade }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @elseif($bulletin->type_bulletin_id == 3)
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th rowspan="2" scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th rowspan="2" scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">№ зачетки</th>
                    <th colspan="4" scope="col" class="w-32 px-2 py-2 font-semibold">Модуль 1</th>
                    <th colspan="4" scope="col" class="w-32 px-2 py-2 font-semibold">Модуль 2</th>
                    <th rowspan="2" scope="col" class="w-32 px-2 py-2 font-semibold">Итоговая оценка</th>
                </tr>
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Посещаемость</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Тестирование</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Практические</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Оценка за модуль</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Посещаемость</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Тестирование</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Практические</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Оценка за модуль</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentGrades as $key => $student)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $student->fio }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $student->ticket_number }}</td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_first_attendance))
                        -
                        @else
                        {{ $student->module_first_attendance }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_first_test_assignment))
                        -
                        @else
                        {{ $student->module_first_test_assignment }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_first_practical_assignment))
                        -
                        @else
                        {{ $student->module_first_practical_assignment }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_first_final_grade))
                        -
                        @else
                        {{ $student->module_first_final_grade }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_second_attendance))
                        -
                        @else
                        {{ $student->module_second_attendance }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_second_test_assignment))
                        -
                        @else
                        {{ $student->module_second_test_assignment }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_second_practical_assignment))
                        -
                        @else
                        {{ $student->module_second_practical_assignment }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->module_second_final_grade))
                        -
                        @else
                        {{ $student->module_second_final_grade }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if($bulletin->control_name == 'Зачет')
                        {{ ($student->grade == 1) ? 'Зачтено' : 'Не зачтено' }}
                        @elseif(is_null($student->grade))
                        -
                        @else
                        {{ $student->grade }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @elseif($bulletin->type_bulletin_id == 1)
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">№ зачетки</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Посещаемость</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Тестирование</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Практические</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Оценка за рейтинг</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Итоговая оценка</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentGrades as $key => $student)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $student->fio }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $student->ticket_number }}</td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->rating_attendance))
                        -
                        @else
                        {{ $student->rating_attendance }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->rating_test_assignment))
                        -
                        @else
                        {{ $student->rating_test_assignment }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->rating_practical_assignment))
                        -
                        @else
                        {{ $student->rating_practical_assignment }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if(is_null($student->rating_final_grade))
                        -
                        @else
                        {{ $student->rating_final_grade }}
                        @endif
                    </td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if($bulletin->control_name == 'Зачет')
                        {{ ($student->grade == 1) ? 'Зачтено' : 'Не зачтено' }}
                        @elseif(is_null($student->grade))
                        -
                        @else
                        {{ $student->grade }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">№ зачетки</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Итоговая оценка</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentGrades as $key => $student)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $student->fio }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $student->ticket_number }}</td>
                    <td class="w-32 px-2 py-2 text-center">
                        @if($bulletin->control_name == 'Зачет')
                        {{ ($student->grade == 1) ? 'Зачтено' : 'Не зачтено' }}
                        @elseif(is_null($student->grade))
                        -
                        @else
                        {{ $student->grade }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>