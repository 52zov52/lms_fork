<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Индивидуальная ведомость') }}</title>
    <style>
        body {
            font-family: 'xo_thames';
        }

        .font-14 {
            font-size: 14px;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-justify {
            text-align: justify;
        }

        .p-1 {
            padding: 0.25rem;
        }

        .table-border-b {
            border-bottom: 1pt solid black;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body class="font-14">
    <h4 class="text-center">Частное образовательное учреждение высшего образования «ЮЖНЫЙ УНИВЕРСИТЕТ (ИУБиП)»</h4>
    <h4 class="text-center">Индивидуальная сводка по успеваемости</h4>
    <p>Студент: {{ $fio }}</p>
    <p>Дата и время печати: {{ \Carbon\Carbon::now()->format('d.m.Y H:m:i') }}</p>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th class="p-1">Дисциплина</th>
                <th class="p-1">Вид контроля</th>
                <th class="p-1">Результат</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentGrades as $key => $data)
            <tr>
                <td colspan="3" class="p-1 text-center font-bold">Семестр {{ $key }}</td>
            </tr>
                @foreach($data as $value)
                <tr>
                    <td class="p-1">{{ $value['discipline_name'] }}</td>
                    <td class="p-1 text-center">{{ $value['control_name'] }}</td>
                    <td class="p-1 text-center">
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
            @endforeach
        </tbody>
    </table>
</body>

</html>