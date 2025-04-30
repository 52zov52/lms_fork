<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        @if(!is_null($data) && count($data) != 0)
        <div class="hidden lg:block border rounded border-slate-400/20">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">Уч. год</th>
                        <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Полугодие</th>
                        <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">
                            @role('Student')
                                Преподаватель
                            @endrole

                            @role('Teacher')
                                Группа
                            @endrole
                        </th>
                        <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">Дисциплина</th>
                        <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Вид занятия</th>
                        <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $value->study_year }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $value->half_year }}</td>
                        <td class="w-24 px-2 py-2 text-center">
                            @role('Student')
                                {{ $value->teacher_fio }}
                            @endrole

                            @role('Teacher')
                                {{ $value->group_name }}
                            @endrole
                        </td>
                        <td class="w-auto px-2 py-2">{{ $value->discipline_name }}</td>
                        <td class="w-24 px-2 py-2 text-center">{{ $value->normative_name }}</td>
                        <td class="w-24 px-2 py-2 text-center">
                            <button class="w-9 btn-sky"
                                wire:click="redirectToFiles({{ $value->year }}, {{ $value->half_year }}, {{ $value->discipline_id }}, {{ $value->normative_id }}, {{ $value->group_id }})"
                                @role('Student') data-tippy-content="Просмотр уч. материалов" @endrole
                                @role('Teacher') data-tippy-content="Загрузить уч. материал(ы)" @endrole>
                                <i class="fa-solid fa-book-bookmark"></i>
                            </button>
                            <button class="w-9 btn-indigo"
                                wire:click="redirectToChats({{ $value->year }}, {{ $value->half_year }}, {{ $value->discipline_id }}, {{ $value->normative_id }}, {{ $value->group_id }})"
                                @role('Student') data-tippy-content="Чат" @endrole
                                @role('Teacher') data-tippy-content="Список чатов" @endrole>
                                <i class="fa-solid fa-comments"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="lg:hidden flex flex-col space-y-2">
            @foreach($data as $value)
            <div class="flex flex-col space-y-2 bg-slate-200 dark:bg-slate-700 border rounded border-slate-200 dark:border-slate-400/20 p-4 text-xs">
                <div class="flex flex-col">
                    <span class="font-semibold">Уч. год и полугодие:</span>
                    <span>{{ $value->study_year }} {{ $value->half_year }}-е полугодие</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">
                        @role('Student')
                            Преподаватель
                        @endrole

                        @role('Teacher')
                            Группа
                        @endrole
                    </span>
                    <span>
                        @role('Student')
                            {{ $value->teacher_fio }}
                        @endrole

                        @role('Teacher')
                            {{ $value->group_name }}
                        @endrole
                    </span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Дисциплина:</span>
                    <span>{{ $value->discipline_name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Вид занятия:</span>
                    <span>{{ $value->normative_name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold">Действия:</span>
                    <div class="flex flex-row space-x-2">
                        <button class="w-auto grow btn-sky"
                            wire:click="redirectToFiles({{ $value->year }}, {{ $value->half_year }}, {{ $value->discipline_id }}, {{ $value->normative_id }}, {{ $value->group_id }})"
                            @role('Student') data-tippy-content="Просмотр уч. материалов" @endrole
                            @role('Teacher') data-tippy-content="Загрузить уч. материал(ы)" @endrole>
                            <i class="fa-solid fa-book-bookmark"></i>
                        </button>
                        <button class="w-auto grow btn-indigo"
                            wire:click="redirectToChats({{ $value->year }}, {{ $value->half_year }}, {{ $value->discipline_id }}, {{ $value->normative_id }}, {{ $value->group_id }})"
                            @role('Student') data-tippy-content="Чат" @endrole
                            @role('Teacher') data-tippy-content="Список чатов" @endrole>
                            <i class="fa-solid fa-comments"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <x-alert type="info" title="Информация" message="Список учебных учебных дисциплин на текущий учебный год отсутствует" />
        @endif
    </div>
</div>