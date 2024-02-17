<div class="flex flex-col space-y-2">

    <table>
        <tr>
            <td>№ пп</td>
            <td>Наименование дисциплины (модуля)</td>
            <td>Семестр</td>
        </tr>
        @foreach($listOfDisciplines as $key => $value)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $value->name }}</td>
            <td>{{ $value->semester_number }}</td>
        </tr>
        @endforeach
    </table>
    
</div>
