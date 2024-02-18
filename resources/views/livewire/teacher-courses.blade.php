<div class="flex flex-col space-y-2">
    <div class="w-full bg-white rounded shadow-xl p-4">
        <table class="w-full bg-white border shadow-md text-xs">
            <tbody>
                <tr class="bg-gray-100">
                    <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                    <td class="w-auto border px-2 py-2 font-semibold">Наименование дисциплины (модуля)</td>
                    <td class="w-32 border px-2 py-2 text-center font-semibold">Кол-во часов лекций</td>
                    <td class="w-32 border px-2 py-2 text-center font-semibold">Кол-во часов практик</td>
                    <td class="w-32 border px-2 py-2 text-center font-semibold">Кол-во часов лабораторных</td>
                    <td class="w-32 border px-2 py-2 text-center font-semibold">Семестр</td>
                    <td class="w-32 border px-2 py-2 text-center font-semibold">Действия</td>
                </tr>
                @foreach($teacherCourses as $key => $value)
                <tr>
                    <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="w-auto border px-2 py-2">{{ $value->name }}</td>
                    <td class="w-32 border px-2 py-2 text-center">{{ $value->lecture_hours }}</td>
                    <td class="w-32 border px-2 py-2 text-center">{{ $value->practical_hours }}</td>
                    <td class="w-32 border px-2 py-2 text-center">{{ $value->laboratory_hours }}</td>
                    <td class="w-32 border px-2 py-2 text-center">{{ $value->semester_number }}</td>
                    <td class="w-32 border px-2 py-2 text-center">
                        <button wire:click="showEJournal({{ $value->id }})" class="bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="Журнал группы">
                            <i class="fa-solid fa-address-book"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>