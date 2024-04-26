<div class="flex flex-col space-y-2">

    <!-- Desktop -->
    <div class="hidden lg:block border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                    <th scope="col" class="w-auto px-2 py-2 font-semibold">Наименование дисциплины (модуля)</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Форма контроля</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Кол-во часов лекций</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Кол-во часов практик</th>
                    <th scope="col" class="w-32 px-2 py-2 font-semibold">Кол-во часов лабораторных</th>
                    <th scope="col" class="w-20 px-2 py-2 font-semibold">Семестр</th>
                    <th scope="col" class="w-20 px-2 py-2 font-semibold">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($teacherCourses as $key => $value)
                <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                    <td class="w-12 px-2 py-2 text-center">{{ $value->id }}</td>
                    <td class="w-auto px-2 py-2">{{ $value->name }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->control_name }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->lecture_hours }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->practical_hours }}</td>
                    <td class="w-32 px-2 py-2 text-center">{{ $value->laboratory_hours }}</td>
                    <td class="w-20 px-2 py-2 text-center">{{ $value->semester_number }}</td>
                    <td class="w-20 px-2 py-2 text-center">
                        <button wire:click="showEJournal({{ $value->id }})" class="bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-indigo-400 font-bold"" title="" data-tippy-content=" Журнал группы">
                            <i class="fa-solid fa-address-book"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Mobile -->
    <div class="lg:hidden flex flex-col space-y-2">
        @foreach($teacherCourses as $key => $value)
        <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
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
                <span class="font-semibold">Семестр:</span>
                <span>{{ $value->semester_number }}</span>
            </div>
            <div class="flex flex-col">
                <span class="font-semibold">Действия:</span>
                <button wire:click="showEJournal({{ $value->id }})" class="w-auto grow bg-white hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-800 border rounded border-slate-200 hover:border-slate-700 dark:border-slate-400/20 px-2 py-1 text-xs text-center text-indigo-400 font-bold" title="" data-tippy-content="Журнал группы">
                    <i class="fa-solid fa-address-book"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>

</div>