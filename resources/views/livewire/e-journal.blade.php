<div class="flex flex-col space-y-2">
    <div class="w-full bg-white rounded shadow-xl p-4">
        <table>
            <tr>
                <td>№</td>
                <td>ФИО</td>
                <td>№ зачетки</td>
                <td>Дата
                    {{ $currentData }}
                    {{ $currentTime }}
                </td>
                <td>Действие</td>
            </tr>
            @foreach($contingent as $key => $value)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $value->last_name }} {{ $value->first_name }} {{ $value->middle_name }}</td>
                <td>{{ $value->ticket_number }}</td>
                <td></td>
                <td></td>
            </tr>
            @endforeach
        </table>

    </div>
</div>