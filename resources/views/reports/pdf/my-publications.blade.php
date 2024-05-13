<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Список трудов') }}</title>
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

        .table-border-b {
            border-bottom: 1pt solid black;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body class="font-14">
    <h2 class="text-center">
        СПИСОК<br>
        опубликованных учебных изданий и научных трудов<br>
        {{ auth()->user()->userData->last_name }} {{ auth()->user()->userData->first_name }} {{ auth()->user()->userData->middle_name }}
    </h2>

    <table border="1" cellspacing="0" cellpadding="0">
        <tbody>
            <tr class="text-center font-bold">
                <td>№ п/п</td>
                <td>Наименование учебных изданий, научных трудов и патентов на изобретения и иные объекты интеллектуальной собственности</td>
                <td>Форма учебных изданий и научных трудов</td>
                <td>Выходные данные</td>
                <td>Объем (п.л.)</td>
                <td>Соавторы</td>
            </tr>
            @foreach($data as $key => $items)
            <tr>
                <td colspan="6" class="text-center">
                    {{ $index[$loop->iteration - 1] }}
                    @if($key == 1)
                    научные работы
                    @elseif($key == 2)
                    учебно-методические работы
                    @else
                    авторские свидетельства, дипломы, патенты, лицензии
                    @endif
                </td>
            </tr>
            @foreach($items as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $item[0] }}</td>
                <td class="text-center">Печатная</td>
                <td>{{ $item[1] }}</td>
                <td class="text-center">{{ $item[2] }}/{{ $item[3] }}</td>
                <td>{{ $item[4] }}</td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td width="33%">Соискатель</td>
            <td width="33%"  class="table-border-b"></td>
            <td width="33%">{{ auth()->user()->userData->last_name }} {{ mb_substr(auth()->user()->userData->first_name, 0, 1) }}.{{ mb_substr(auth()->user()->userData->middle_name, 0, 1) }}.</td>
        </tr>
        <tr>
            <td width="33%"></td>
            <td width="33%"  class="text-center">(подпись)</td>
            <td width="33%"></td>
        </tr>
    </table>
    <p>Дата печати: {{ \Carbon\Carbon::now()->format('d.m.Y H:m:i') }}</p>
</body>

</html>