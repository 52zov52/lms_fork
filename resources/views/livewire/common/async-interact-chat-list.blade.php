<div class="h-full">
    <div class="h-full flex flex-col space-y-2">
        <h4 class="text-base lg:text-base font-semibold">
            Группа: {{ $group }}
        </h4>
        <h4 class="text-base lg:text-base font-semibold">
            Дисциплина: {{ $discipline }} ({{ $normative }})
        </h4>
        <h4 class="text-base lg:text-base font-semibold">
            Учебный год: {{ $studyYear }} {{ $halfYear }}-е полугодие
        </h4>
        @if(!is_null($students) && count($students) != 0)
        <div class="border rounded border-slate-400/20">
            <table class="w-full text-xs md:text-sm">
                <thead class="bg-slate-200 dark:bg-slate-700 uppercase">
                    <tr class="border-b border-slate-400/20">
                        <th scope="col" class="w-12 px-2 py-2 text-center font-semibold">№ пп</th>
                        <th scope="col" class="w-auto px-2 py-2 text-left font-semibold">ФИО</th>
                        <th scope="col" class="w-24 px-2 py-2 text-center font-semibold">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $value)
                    <tr class="bg-white hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 border-b border-slate-400/20">
                        <td class="w-12 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                        <td class="w-auto px-2 py-2 text-left">{{ $value->fio }}</td>
                        <td class="w-24 px-2 py-2 text-center">
                            <Button class="w-9 btn-indigo"
                                wire:click="redirectToChat({{ $value->user_id }})"
                                data-tippy-content="Чат">
                                <i class="fa-solid fa-comments"></i>
                            </Button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <x-alert type="info" title="Информация" message="Список чатов по дисциплине на текущий учебный год отсутствует" />
        @endif
    </div>
</div>