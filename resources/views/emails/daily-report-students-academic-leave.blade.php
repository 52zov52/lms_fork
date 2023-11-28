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
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fafafa;
        }
    </style>
</head>

<body>
    <div style="margin-top: 50px;">
        <table border="1">
            <body>
                <tr>
                    <td>№</td>
                    <td>ФИО</td>
                    <td>Группа</td>
                    <td>Дата выхода из академ. отпуска</td>
                    <td>Комментарий</td>
                </tr>
                @foreach($studentsAcademicLeaves as $student)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name }}</td>
                    <td>{{ $student->group_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($student->academic_leave_end_date)->format('d.m.Y') }}</td>
                    <td>{{ $student->description }}</td>
                </tr>
                @endforeach
            </body>
        </table>
    </div>
</body>

</html>