<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <style>
        html {
            font-family: sans-serif;
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            -ms-overflow-style: scrollbar;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        body {
            margin: 0;
            width: 100%;
            font-family: "Roboto", "Helvetica", "Arial", sans-serif;
            font-size: 0.70rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fafafa;
        }

        table,
        th,
        td {
            border: 1px solid black;
            border-collapse: collapse;
            padding: 0.25rem;
        }

        th {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div style="margin: 0.25rem;">
        <table style="width: 100%;" border="1">
            <body>
                <tr>
                    <th>Событие (задача)</th>
                    <th>Срок исполнения до</th>
                    <th>Статус</th>
                </tr>
                <tr>
                    <td colspan="3">Решения ректората</td>
                </tr>
                @if(count($rectorateDecisionEvents) == 0)
                <tr>
                    <td colspan="3">Комментарий: задачи отсутствуют</td>
                </tr>
                @else
                    @foreach($rectorateDecisionEvents as $rectorateDecisionEvent)
                    <tr>
                        <td>{{ $rectorateDecisionEvent->task }}</td>
                        <td>{{ \Carbon\Carbon::parse($rectorateDecisionEvent->due_date)->format('d.m.Y') }}</td>
                        <td>{{ ($rectorateDecisionEvent->due_date == \Carbon\Carbon::now()->format('Y-m-d')) ? 'Сегодня' : 'Ближайшие дни' }}</td>
                    </tr>
                    @endforeach
                @endif
                <tr>
                    <td colspan="3">Воспитательная работа</td>
                </tr>
                @if(count($upbringingEvents) == 0)
                <tr>
                    <td colspan="3">Комментарий: события отсутствуют</td>
                </tr>
                @else
                    @foreach($upbringingEvents as $upbringingEvent)
                    <tr>
                        <td>{{ $upbringingEvent->name }}</td>
                        <td>{{ \Carbon\Carbon::parse($upbringingEvent->date)->format('d.m.Y') }}</td>
                        <td>{{ ($upbringingEvent->date == \Carbon\Carbon::now()->format('Y-m-d')) ? 'Сегодня' : 'Ближайшие дни' }}</td>
                    </tr>
                    @endforeach
                @endif
            </body>
        </table>
    </div>
</body>

</html>