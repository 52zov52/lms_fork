<div class="flex flex-col space-y-2">
    <div class="w-full bg-white rounded shadow-xl p-4">
        <div class="hidden md:block lg:block xl:block">
            <table class="w-full bg-white border shadow-md text-xs xl:text-base">
                <tbody>
                    <tr>
                        <td class="w-12 border px-2 py-2 text-center font-semibold">№ пп</td>
                        <td class="w-auto border px-2 py-2 font-semibold">Наименование дисциплины (модуля)</td>
                        <td class="w-32 border px-2 py-2 text-center font-semibold">Форма контроля</td>
                        <td class="w-32 border px-2 py-2 text-center font-semibold">Кол-во часов лекций</td>
                        <td class="w-32 border px-2 py-2 text-center font-semibold">Кол-во часов практик</td>
                        <td class="w-32 border px-2 py-2 text-center font-semibold">Кол-во часов лабораторных</td>
                        <td class="w-20 border px-2 py-2 text-center font-semibold">Полугодие</td>
                        <td class="w-20 border px-2 py-2 text-center font-semibold">Действия</td>
                    </tr>
                    @foreach($teacherCourses as $key => $value)
                    <tr class="{{ ($loop->iteration % 2 === 0) ? 'bg-gray-100' : '' }} hover:bg-gray-300">
                        <td class="w-12 border px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto border px-2 py-2">{{ $value->name }}</td>
                        <td class="w-32 border px-2 py-2 text-center">{{ $value->control_name }}</td>
                        <td class="w-32 border px-2 py-2 text-center">{{ $value->lecture_hours }}</td>
                        <td class="w-32 border px-2 py-2 text-center">{{ $value->practical_hours }}</td>
                        <td class="w-32 border px-2 py-2 text-center">{{ $value->laboratory_hours }}</td>
                        <td class="w-20 border px-2 py-2 text-center">{{ $value->half_year }}</td>
                        <td class="w-20 border px-2 py-2 text-center">
                            <button wire:click="showEJournal({{ $value->id }})" class="bg-blue-500 hover:bg-blue-600 rounded shadow-xl px-1.5 py-1.5 text-xs text-white" title="" data-tippy-content="Журнал группы">
                                <i class="fa-solid fa-address-book"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="block md:hidden lg:hidden xl:hidden flex flex-col space-y-2">
            @foreach($teacherCourses as $key => $value)
            <div class="flex flex-col space-y-2 border shadow-md p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Наименование дисциплины (модуля):</span>
                    <span>{{ $value->name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Форма контроля:</span>
                    <span>{{ $value->control_name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Кол-во часов лекций:</span>
                    <span>{{ $value->lecture_hours }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Кол-во часов практик:</span>
                    <span>{{ $value->practical_hours }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Кол-во часов лабораторных:</span>
                    <span>{{ $value->laboratory_hours }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Полугодие:</span>
                    <span>{{ $value->half_year }}</span>
                </div>
                <div class="flex flex-col">
                    <button wire:click="showEJournal({{ $value->id }})" class="w-auto grow bg-blue-500 hover:bg-blue-600 rounded px-2 py-1 text-white" title="" data-tippy-content="Журнал группы">
                        <i class="fa-solid fa-address-book"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>