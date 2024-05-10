<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Ведомость промежуточной аттестации') }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;

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
    <h4 class="text-center">ВЕДОМОСТЬ ПРОМЕЖУТОЧНОЙ АТТЕСТАЦИИ</h4>
    <h4 class="text-center">Направление подготовки {{ $infoAboutGroupAndCourse->specialty_code }} {{ $infoAboutGroupAndCourse->speciality_name }}</h4>
    <table>
        <tbody>
            <tr>
                <td>Учебный год</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->academic_year }}</td>
            </tr>
            <tr>
                <td>Семестр</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->semester_number }}</td>
            </tr>
            <tr>
                <td>Форма обучения</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->teaching_form_name }}</td>
            </tr>
            <tr>
                <td>Группа</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->group_name }}</td>
            </tr>
            <tr>
                <td>Дисциплина</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->discipline_name }}</td>
            </tr>
            <tr>
                <td>Форма промежуточной аттестации</td>
                <td class="table-border-b">{{ $infoAboutGroupAndCourse->control_name }}</td>
            </tr>
            <tr>
                <td>Преподаватель</td>
                <td class="table-border-b">{{ auth()->user()->userData->last_name }} {{ auth()->user()->userData->first_name }} {{ auth()->user()->userData->middle_name }}</td>
            </tr>
            <tr>
                <td>Дата провеления</td>
                <td class="table-border-b"></td>
            </tr>
        </tbody>
    </table>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th rowspan="2">№ п/п</th>
                <th rowspan="2">Фамилия И.О.</th>
                <th rowspan="2">Номер зачетной книжки</th>
                <th colspan="2">Оценка</th>
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
                <td class="text-center">
                    @switch($value->status)
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
                    @endswitch</td>
                <td class="text-center">
                    @switch($value->status)
                        @case(2)
                            н/я
                        @break
                        @case(4)
                            Неудовлетворительно
                        @break
                        @case(5)
                            Удовлетворительно
                        @break
                        @case(6)
                            Хорошо
                        @break
                        @case(7)
                            Отлично
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
            </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr>
                <td>Число студентов на промежуточной аттестации</td>
                <td class="table-border-b">{{ $data->where('status', '!=', 2)->count() }}</td>
            </tr>
            <tr>
                <td>Из них получивших «отлично»/«зачтено»</td>
                <td class="table-border-b">{{ $data->where(function ($query) { 
                        $query->where('status', '=', 7)
                        ->orWhere('status', '=', 8);
                    })->count() }}
                </td>
            </tr>
            <tr>
                <td>получивших «хорошо»</td>
                <td class="table-border-b">{{ $data->where('status', 6)->count() }}</td>
            </tr>
            <tr>
                <td>получивших «удовлетворительно»</td>
                <td class="table-border-b">{{ $data->where('status', 5)->count() }}</td>
            </tr>
            <tr>
                <td>получивших «неудовлетворительно»/«не зачтено»</td>
                <td class="table-border-b">{{ $data->where(function ($query) { 
                        $query->where('status', '=', 4)
                        ->orWhere('status', '=', 9);
                    })->count() }}
                </td>
            </tr>
            <tr>
                <td>Число студентов, не явившихся на промежуточную аттестацию</td>
                <td class="table-border-b">{{ $data->where('status', 2)->count() }}</td>
            </tr>
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tbody>
            <tr>
                <td width="50%">Подпись преподавателя</td>
                <td width="50%" class="table-border-b"></td>
            </tr>
            <tr>
                <td width="50%">Подпись академического директора</td>
                <td width="50%" class="table-border-b"></td>
            </tr>
        </tbody>
    </table>
</body>

</html>