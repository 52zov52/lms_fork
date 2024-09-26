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

        .p-1 {
            padding: 0.25rem;
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
    <h4 class="text-center">Анкета для составления социального паспорта</h4>
    <table width="100%" border="1" cellspacing="0" cellpadding="0">
        <tbody>
            <tr>
                <td width="30%" class="p-1">ФИО</td>
                <td width="70%" class="p-1">{{ $socialPassport->last_name }} {{ $socialPassport->first_name }} {{ $socialPassport->middle_name }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Дата рождения</td>
                <td width="70%" class="p-1">{{ \Carbon\Carbon::parse($socialPassport->birthday)->format('d.m.Y') }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Место жительства (по прописке)</td>
                <td width="70%" class="p-1">{{ $socialPassport->registered_address }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Жилищные условия</td>
                <td width="70%" class="p-1">{{ $socialPassport->housing_conditions }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">С кем совместно проживаете</td>
                <td width="70%" class="p-1">
                    @foreach($cohabitants as $cohabitant)
                    <label>
                        <input value="{{ $cohabitant }}" type="checkbox" @if(str_contains($socialPassport->cohabitants, $cohabitant)) checked @endif>
                        <span>{{ $cohabitant }}</span>
                    </label>
                    @endforeach
                </td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Состав семьи</td>
                <td width="70%" class="p-1">{{ $socialPassport->family_composition }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Количество детей в семье</td>
                <td width="70%" class="p-1">{{ $socialPassport->number_of_children }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">ФИО отца</td>
                <td width="70%" class="p-1">{{ $socialPassport->father_last_name }} {{ $socialPassport->father_first_name }} {{ $socialPassport->father_middle_name }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Место работы отца</td>
                <td width="70%" class="p-1">{{ $socialPassport->father_workplace }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Контактный телефон отца</td>
                <td width="70%" class="p-1">{{ $socialPassport->father_phone }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">ФИО матери</td>
                <td width="70%" class="p-1">{{ $socialPassport->mother_last_name }} {{ $socialPassport->mother_first_name }} {{ $socialPassport->mother_middle_name }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Место работы матери</td>
                <td width="70%" class="p-1">{{ $socialPassport->mother_workplace }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Контактный телефон матери</td>
                <td width="70%" class="p-1">{{ $socialPassport->mother_phone }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Наличие инвалидности у отца</td>
                <td width="70%" class="p-1">{{ ($socialPassport->father_disability) ? 'Да' : 'Нет' }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Наличие инвалидности у матери</td>
                <td width="70%" class="p-1">{{ ($socialPassport->mother_disability) ? 'Да' : 'Нет' }}</td>
            </tr>
            <tr>
                <td width="30%" class="p-1">Относитесь ли вы к одной из перечисленных категорий</td>
                <td width="70%" class="p-1">
                    @foreach($childCategories as $childCategory)
                    <label>
                        <input value="{{ $childCategory }}" type="checkbox" class="mr-1" @if(str_contains($socialPassport->child_categories, $childCategory)) checked @endif>
                        <span>{{ $childCategory }}</span>
                    </label>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>