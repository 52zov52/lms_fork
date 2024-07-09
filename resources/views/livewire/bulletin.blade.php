<div class="flex flex-col space-y-2">

    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">ФИО</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">№ зачетки</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Оценка</th>
                </tr>
            </thead>
            <tbody>
                @foreach($studentGrades as $key => $student)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto px-2 py-2">{{ $student->fio }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $student->ticket_number }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $student->grade }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>