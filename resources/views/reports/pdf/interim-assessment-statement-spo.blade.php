<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ведомость промежуточной аттестации</title>
    <style>
        body {
            font-family: 'xo_thames';
            font-size: 11px;
        }

        .text-center {
            text-align: center;
        }

        .text-indent {
            text-indent: 2rem;
        }

        .table-border-b {
            border-bottom: 0.5pt solid black;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <h3 class="text-center">
        Частное образовательное учреждение высшего образования «ЮЖНЫЙ УНИВЕРСИТЕТ (ИУБиП)»<br>
        {{ $wsp->teaching_form_name }} форма обучения<br>
        ЗАЧЕТНО-ЭКЗАМЕНАЦИОННАЯ ВЕДОМОСТЬ № {{ $bulletinNumber }}
    </h3>
    <br>
    <table>
        <tbody>
            <tr>
                <td>Учебный год</td>
                <td class="table-border-b">{{ $studyYear }}</td>
            </tr>
            <tr>
                <td>Семестр</td>
                <td class="table-border-b">{{ $wsp->semester_number }}</td>
            </tr>
            <tr>
                <td>Форма контроля</td>
                <td class="table-border-b">{{ $wsp->control_name }}</td>
            </tr>
            <tr>
                <td>Дисциплина</td>
                <td class="table-border-b">{{ $wsp->discipline_name }}</td>
            </tr>
            <tr>
                <td>Специальность</td>
                <td class="table-border-b">{{ $wsp->specialty_code }} {{ $wsp->speciality_name }}</td>
            </tr>
            <tr>
                <td>Группа</td>
                <td class="table-border-b">{{ $group->group_name }}</td>
            </tr>
            <tr>
                <td>Преподаватель</td>
                <td class="table-border-b">{{ $teacherFullname }}</td>
            </tr>
            <tr>
                <td>Дата провеления экзамена (зачета)</td>
                <td class="table-border-b">{{ $examDate }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th rowspan="2">№ п/п</th>
                <th rowspan="2">Фамилия И.О.</th>
                <th rowspan="2">Номер зачетной книжки</th>
                <th rowspan="2">Контрольные работы</th>
                <th colspan="2">Экзаменационная оценка</th>
                <th rowspan="2">Подпись</th>
            </tr>
            <tr>
                <th>цифрой</th>
                <th>прописью</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $value)
            <tr>
                <td class="text-center">{{ $loop->iteration}}</td>
                <td>
                    {{ $value->last_name }}
                    {{ $value->first_name }}
                    {{ $value->middle_name }}
                </td>
                <td class="text-center">{{ $value->ticket_number }}</td>
                <td class="text-center"> - </td>
                <td class="text-center">
                    @switch($value->status)
                        @case(2)
                            н/я
                            @break
                        @case(6)
                            2
                            @break
                        @case(7)
                            3
                            @break
                        @case(8)
                            4
                            @break
                        @case(9)
                            5
                            @break
                        @case(10)
                            Зачтено
                            @break
                        @case(11)
                            Не зачтено
                            @break
                        @default
                            -
                    @endswitch</td>
                <td class="text-center">
                    @switch($value->status)
                        @case(2)
                            н/я
                            @break
                        @case(6)
                            Неудовлетворительно
                            @break
                        @case(7)
                            Удовлетворительно
                            @break
                        @case(8)
                            Хорошо
                            @break
                        @case(9)
                            Отлично
                            @break
                        @case(10)
                            Зачтено
                            @break
                        @case(11)
                            Не зачтено
                            @break
                        @default
                            -
                    @endswitch
                </td>
                <td class="text-center">{{ $teacherShortname }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr>
                <td width="50%">Число студентов на промежуточной аттестации</td>
                <td width="50%" class="table-border-b">{{ $data->whereNotIn('status', [2, 3, 11])->count() }}</td>
            </tr>
            <tr>
                <td width="50%">Из них получивших «отлично»/«зачтено»</td>
                <td width="50%" class="table-border-b">{{ $data->whereIn('status', [9, 10])->count() }}</td>
            </tr>
            <tr>
                <td width="50%" class="text-indent">получивших «хорошо»</td>
                <td width="50%" class="table-border-b">{{ $data->where('status', 8)->count() }}</td>
            </tr>
            <tr>
                <td width="50%" class="text-indent">получивших «удовлетворительно»</td>
                <td width="50%" class="table-border-b">{{ $data->where('status', 7)->count() }}</td>
            </tr>
            <tr>
                <td width="50%" class="text-indent">получивших «неудовлетворительно»/«не зачтено»</td>
                <td width="50%" class="table-border-b">{{ $data->whereIn('status', [6, 11])->count() }}</td>
            </tr>
            <tr>
                <td width="50%">Число студентов, не явившихся на промежуточную аттестацию</td>
                <td width="50%" class="table-border-b">{{ $data->where('status', 2)->count() }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr>
                <td width="50%">Подпись преподавателя</td>
                <td width="50%" class="table-border-b">{{ $teacherShortname }}</td>
            </tr>
            <tr>
                <td width="50%">Подпись академического директора</td>
                <td width="50%" class="table-border-b"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>