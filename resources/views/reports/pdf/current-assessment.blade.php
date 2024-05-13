<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Ведомость текущего контроля успеваемости') }}</title>
    <style>
        body {
            font-family: 'xo_thames';
        }

        .font-9 {
            font-size: 9px;
        }

        .font-10 {
            font-size: 10px;
        }

        .font-11 {
            font-size: 11px;
        }

        .font-12 {
            font-size: 12px;
        }

        .font-14 {
            font-size: 14px;
        }

        .font-bold {
            font-weight: bold;
        }

        .font-italic {
            font-style: italic;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .table-border-b {
            border-bottom: 1pt solid black;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body class="font-10">
    <h4 class="text-center">Частное образовательное учреждение высшего образования «ЮЖНЫЙ УНИВЕРСИТЕТ (ИУБиП)»</h4>
    <h4 class="text-center">ВЕДОМОСТЬ КОНТРОЛЯ УСПЕВАЕМОСТИ</h4>
    <table width="100%">
        <tbody>
            <tr>
                <td>Направление подготовки</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->specialty_code }} {{ $infoAboutGroupAndCourse->speciality_name }}</td>
                <td>Форма обучения</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->teaching_form_name }}</td>
            </tr>
            <tr>
                <td>Группа</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->group_name }}</td>
                <td>Учебный год</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->academic_year }}</td>
            </tr>
            <tr>
                <td>Дисциплина</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->discipline_name }}</td>
                <td>Семестр</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->semester_number }}</td>
            </tr>
            <tr>
                <td>Преподаватель</td>
                <td class="table-border-b">{{ auth()->user()->userData->last_name }} {{ auth()->user()->userData->first_name }} {{ auth()->user()->userData->middle_name }}</td>
                <td>Форма промежуточной аттестации</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->control_name }}</td>
            </tr>
            <tr>
                <td>Дата провеления</td>
                <td class="table-border-b"></td>
                <td></td>
                <td></td>
            </tr>
        </tbody>
    </table>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th rowspan="2">№ п/п</th>
                <th rowspan="2">Фамилия И.О.</th>
                <th rowspan="2">№ зачетных книжек</th>
                <th>1-я контрольная точка</th>
                <th>2-я контрольная точка</th>
                <th>3-я контрольная точка</th>
                <th colspan="2">3-я контрольная точка: компьютерное тестирование</th>
                <th rowspan="2">Результат текущего контроля</th>
                <th rowspan="2">Оценка за ответ на экзамене/диф. зачёта/зачёте</th>
            </tr>
            <tr>
                <th>оценка</th>
                <th>оценка</th>
                <th>оценка</th>
                <th>оценка</th>
                <th>% правильных ответов</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $value)
            <tr>
                <td class="text-center">{{ $loop->iteration}}</td>
                <td>
                    {{ $value['fio'] }}
                </td>
                <td class="text-center">{{ $value['ticket_number'] }}</td>
                @foreach($value['lesson_type_tk'] as $element)
                <td class="text-center">
                    @switch($element)
                        @case(2)
                            н/я
                        @break
                        @case(4)
                            2
                        @break
                        @case(5)
                            3
                        @break
                        @case(6)
                            4
                        @break
                        @case(7)
                            5
                        @break
                        @default
                        -
                    @endswitch
                </td>
                @endforeach
                <td class="text-center">
                    @switch($value['lesson_type_test'])
                        @case(2)
                            н/я
                        @break
                        @case(4)
                            2
                        @break
                        @case(5)
                            3
                        @break
                        @case(6)
                            4
                        @break
                        @case(7)
                            5
                        @break
                        @case(8)
                            Зачтено
                        @break
                        @case(9)
                            Не зачтено
                        @break
                        @default
                        -
                    @endswitch
                </td>
                <td class="text-center"></td>
                <td class="text-center">
                    {{ $value['result_tk'] }}
                </td>
                <td class="text-center">
                    @switch($value['lesson_type_exam'])
                        @case(2)
                            н/я
                        @break
                        @case(4)
                            2
                        @break
                        @case(5)
                            3
                        @break
                        @case(6)
                            4
                        @break
                        @case(7)
                            5
                        @break
                        @case(8)
                            Зачтено
                        @break
                        @case(9)
                            Не зачтено
                        @break
                        @default
                        -
                    @endswitch
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>