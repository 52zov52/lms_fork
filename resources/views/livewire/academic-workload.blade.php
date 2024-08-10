<div class="flex flex-col space-y-2">

    <h4 class="text-base lg:text-base font-semibold">
        {{ $academicYear }} учебный год
    </h4>

    @if(!is_null($data) && count($data) != 0)
    <div class="border rounded border-slate-400/20">
        <table class="w-full text-xs lg:text-sm">
            <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                <tr class="border-b border-slate-400/20">
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Дисциплина</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Тип работы</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Подгруппа</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Группа</th>
                    <th scope="col" class="w-16 px-2 py-2 font-semibold">Кол-во студентов</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
    @else
    <x-alert type="info" title="Информация" message="Учебная нагрузка отсутствует" />
    @endif
</div>