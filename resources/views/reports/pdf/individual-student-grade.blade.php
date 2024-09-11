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

        .table-border-b {
            border-bottom: 1pt solid black;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body class="font-14">
    <p>Дата печати: {{ \Carbon\Carbon::now()->format('d.m.Y H:m:i') }}</p>
</body>

</html>