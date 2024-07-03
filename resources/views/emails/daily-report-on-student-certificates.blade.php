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

        p {
            text-align: center;
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
                    <th>Кол-во заявок СПО за текущий день</th>
                    <th>Кол-во заявок СПО всего (в работе)</th>
                    <th>Кол-во заявок ВО за текущий день</th>
                    <th>Кол-во заявок ВО всего (в работе)</th>
                </tr>
                <tr>
                    <td><p>{{ $orderRequestSPO->current_count_requests }}</p></td>
                    <td><p>{{ $orderRequestSPO->total_count_requests }}</p></td>
                    <td><p>{{ $orderRequestVO->current_count_requests }}</p></td>
                    <td><p>{{ $orderRequestVO->total_count_requests }}</p></td>
                </tr>
            </body>
        </table>
    </div>
</body>

</html>