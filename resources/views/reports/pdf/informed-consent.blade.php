<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Информированное согласие') }}</title>
    <style>
        body {
            font-family: 'xo_thames';
        }

        .font-10 {
            font-size: 10px;
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

        .p-text-indent {
            text-align: justify;
            text-indent: 20px;
            margin: 0;
        }

        .border-rounded {
            border-radius: 10px;
            border: 1px solid black;
            display: inline-block;
            padding: 10px;
            margin: 0;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body class="font-14">
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td width="50%"></td>
                <td width="50%">
                    Проректору по ВРиМП ЧОУ ВО ЮУ (ИУБиП)<br>
                    А.И. Фоменко<br>
                    {{ $fioGenitive }}<br>
                    обучающийся(аяся) {{ $course }} курса {{ $teachingForm }} формы обучения<br>
                    направление подготовки/специальность<br>
                    {{ $speciality }}
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <h4 class="text-center">ИНФОРМИРОВАННОЕ СОГЛАСИЕ<br>обучающегося</h4>
    <p class="p-text-indent">Я, нижеподписавшийся(аяся), {{ $fio }} {{ $birthday }} года рождения, добровольно даю согласие на участие в социально-психологическом тестировании, направленном раннее выявление незаконного потребления наркотических средств и психотропных веществ.</p>
    <p class="p-text-indent">Я получил(а) объяснения о цели тестирования, о его длительности, а также информацию о возможных результатах тестирования. Мне была предоставлена возможность задавать вопросы, касающиеся тестирования. Я полностью удовлетворен(а) полученными сведениями.</p>
    <p class="p-text-indent">Я согласен(на) выполнять инструкции, полученные от уполномоченного лица, проводяшего тестирование.</p>
    <br>
    <div class="border-rounded font-10">
        <p>Документ подписан простой электронной подписью</p>
        <p>ФИО: {{ $fio }}</p>
        <p>Дата и время подписания: {{ $updatedAt }}</p>
    </div>
</body>

</html>