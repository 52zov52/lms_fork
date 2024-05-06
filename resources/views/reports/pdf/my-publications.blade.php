<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Список трудов') }}</title>
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

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body class="font-10">
    <h2 class="text-center">СПИСОК<br>опубликованных учебных изданий и научных трудов<br>{{ auth()->user()->userData->last_name }} {{ auth()->user()->userData->first_name }} {{ auth()->user()->userData->middle_name }}</h2>

    <table border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>№ п/п</th>
                <th>Наименование учебных изданий, научных трудов и патентов на изобретения и иные объекты интеллектуальной собственности</th>
                <th>Форма учебных изданий и научных трудов</th>
                <th>Выходные данные</th>
                <th>Объем (п.л.)</th>
                <th>Соавторы</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $key => $value)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $value->title }}</td>
                <td>Печатная</td>
                <td>{{ $value->output_data }}</td>
                <td>{{ $value->volume_full_in_printed_sheets }}/{{ $value->volume_full_on_pages }}</td>
                <td>{{ $value->name_co_authors }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>