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

        table, th, td {
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
                    <th>ФИО</th>
                    <th>Группа*</th>
                    <th>Дата выхода из академ. отпуска</th>
                    <th>Статус</th>
                </tr>
                @foreach($studentsAcademicLeaves as $student)
                <tr>
                    <td>{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</td>
                    <td>{{ $student->group_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($student->academic_leave_end_date)->format('d.m.Y') }}</td>
                    <td>{{ ($student->academic_leave_end_date == \Carbon\Carbon::now()->format('Y-m-d')) ? 'Сегодня' : 'Ближайшие дни' }}</td>
                </tr>
                <tr><td colspan="4">Комментарий: {{ $student->description }}</td></tr>
                @endforeach
            </body>
        </table>
        <p style="line-height: 1.25rem; color: rgb(239 68 68);">*группа, в которой был студент, но с актуальным наименованием</p>
    </div>
</body>

</html>